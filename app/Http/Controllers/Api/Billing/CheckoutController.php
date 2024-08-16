<?php

namespace App\Http\Controllers\Api\Billing;

use App\Actions\Orders\CreateCard;
use App\Actions\Orders\DoCheckout;
use App\Exceptions\Pagarme\CardCreationException;
use App\Exceptions\Pagarme\OrderCreationException;
use App\Exceptions\Pagarme\TokenNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Billing\OrderResource;
use App\Http\Resources\Billing\ProductResource;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use App\ValueClasses\OrderPaymentMethods;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{

    protected $service;

    /**
     * Campos a serem recebidos na request de checkout do pedido
     */
    protected $paymentFields = [
        'boleto' => ['profile_id'],
        'card' => ['card', 'profile_id', 'installments'],
        'pix' => ['profile_id']
    ];

    /**
     * List all plans registered in the database
     *
     * @return \Illuminate\Http\Response
     */
    public function plans()
    {
        $plans = Product::ofPlans()->where('is_active',1)->get();
        $plans = ProductResource::collection($plans);
     
        return response()->json(compact('plans'), 200);
    }

    /**
     * List all discount coupons an user have
     * @param Illuminate\Http\Request $request
     */
    public function coupons(Request $request)
    {
        $coupons = $request->user()->coupons;
        return response()->json(compact('coupons'), 200);
    }

    /**
     * Apply a discount coupon to the order
     */
    public function applyCoupon(Request $request)
    {
        $order = $request->user()->cart;
        $coupon = Coupon::where('code', $request->code)->firstOrFail();
        // Se o usuario for membro da tropa, associa o cupom de desconto a ele.
        if ($request->user()->is_trooper()) {
            $coupon->users()->syncWithoutDetaching($request->user()->id);
        }
        $order->applyCoupon($coupon);
        return response()->json(['message' => 'Cupom aplicado com sucesso', 'success' => true], 200);
    }

    /**
     * Remove a discount coupon from the order
     */
    public function removeCoupon(Request $request)
    {
        $order = $request->user()->cart;
        $coupon = Coupon::where('code', $request->code)->firstOrFail();
        $order->discounts()->where('coupon_id', $coupon->id)->delete();
        return response()->json(['message' => 'Cupom removido com sucesso', 'success' => true], 200);
    }

    /**
     * List all orders from the current user
     * 
     * @param \Illuminate\Http\Request
     */
    public function orders(Request $request)
    {
        $orders = $request->user()->orders()->orderBy('id', 'DESC')->paginate(10);
        return response()->json(compact('orders'), 200);
    }

    /**
     * Get current "shopping cart" details
     * 
     * Shopping Cart is the user's most recent order
     * that the status still 'checkout_pending'
     */
    public function cart(Request $request)
    {
        $cart = $request->user()->cart;
        // se o carrinho ainda não tiver sido persistido
        if (!$cart->exists) {
            $cart->save();
        }
        $cart->load('items', 'discounts');
        $cart = new OrderResource($cart);
        return response()->json(compact('cart'), 200);
    }

    /**
     * Get details about the specified order
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getOrder(Request $request, $id)
    {
        $order = $request->user()->orders()->findOrFail($id);
        $order->load('items', 'latestUpdate', 'transaction', 'payer', 'invoice.paymentMethod');
        return response()->json(compact('order'), 200);
    }


    /**
     * Update cart details
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateCart(Request $request)
    {
        $cart = $request->user()->cart;

        $request->collect('items')->map(function ($item) use ($cart) {
            $cart->items()->updateOrCreate(
                ['product_id' => $item['product_id']],
                $item
            );
        });
        // Atualiza os itens no pedido no objeto do carrinho
        $cart->refresh();
        $cart = new OrderResource($cart);
        return response()->json(compact('cart'), 200);
    }

    /**
     * Delete items from user's cart
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteItems(Request $request)
    {
        $cart = $request->user()->cart;
        $cart->items()->delete($request->collect('id'));
        $cart->refresh();
        $cart = new OrderResource($cart);
        return response()->json(compact('cart'), 200);
    }

    /**
     * Delete a cart and all its items
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteCart(Request $request)
    {
        $cart = $request->user()->cart;
        $cart->items()->delete();
        $cart->discounts()->delete();
        $cart->delete();
        return response()->json(['message' => 'Carrinho removido com sucesso', 'success' => true], 200);
    }

    /**
     * Realiza o checkout do usuário
     * @param \Illuminate\Http\Request $request
     * @param string $method
     * @return \Illuminate\Http\Response
     */
    public function checkout(Request $request, $method)
    {
        // valida se o usuário já tem uma subscription válida.
        if ($request->user()->is_premium()) {
            return response()->json(['message' => 'Usuário já possui uma subscription ativa.', 'success' => false], 401);
        } else if ($request->user()->order->status == Order::PROCESSING) {
            return response()->json(['message' => 'Você já possui um pedido em processamento. Por favor, aguarde!', 'success' => false], 420);
        }

        $userData = $request->only($this->paymentFields[$method]);
        // cria uma nova subscription no DB (para user no processo de pagamento)
        $order = $request->user()->cart;
        $order->save(); // para garantir que o pedido terá as propriedades code e status
        $payer = $request->user()->billingProfiles()->findOrFail($userData['profile_id']);
        $order->setPayer($payer);
        $paymentMethod = null;
        switch (OrderPaymentMethods::getMethodName($method)) {
            case OrderPaymentMethods::CARD:
                $paymentMethod = CreateCard::make()->handle($payer, $userData['card']);
                $order->setInstallments($userData['installments']);
                $order->setPaymentMethod($paymentMethod);
                break;
            case OrderPaymentMethods::BOLETO:
                $paymentMethod = $request->user()->paymentMethods()->create(['type' => OrderPaymentMethods::BOLETO, 'billing_profile_id' => $payer->id, 'gateway' => 'pagarme']);
                $order->setInstallments(1);
                $order->setPaymentMethod($paymentMethod);
                break;
            case OrderPaymentMethods::PIX:
                $paymentMethod = $request->user()->paymentMethods()->create(['type' => OrderPaymentMethods::PIX, 'billing_profile_id' => $payer->id, 'gateway' => 'pagarme']);
                $order->setInstallments(1);
                $order->setPaymentMethod($paymentMethod);
                break;
            default:
                return response()->json(['message' => 'Método de pagamento inválido!', 'success' => false], 400);
                break;
        }

        try {
            $order = DoCheckout::make()->handle($order);
            // DB::commit();
            return response()->json(['order' => new OrderResource($order), 'success' => true], 200);
        } catch (OrderCreationException $e) {
            // DB::commit();
            Log::warning("Erro ao criar pedido: {$e->getMessage()} ", ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return response()->json(['order' => new OrderResource($order), 'success' => false], 200);
        } catch (TokenNotFoundException $e) {
            // DB::commit();
            Log::debug($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return response()->json(['message' => 'O token do cartão de crédito informado expirou.', 'success' => false], 412);
        } catch (CardCreationException  $e) {
            // DB::rollBack();
            Log::debug($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return response()->json(['message' => $e->getMessage(), 'success' => false], 412);
        } catch (\Exception $th) {
            // DB::rollBack();
            Log::error("Erro não tratado durante subscription: {$th->getMessage()}", ['file' => $th->getFile(), 'line' => $th->getLine()]);
            return response()->json(['message' => 'Erro ao processar pedido', 'success' => false], 500);
        }
    }


    public function getLatestOrder(Request $request)
    {
        $order = $request->user()->orders()->orderBy('id', 'DESC')->firstOrFail();
        $order = new OrderResource($order);
        return response()->json(['order' => $order, 'success' => true], 200);
    }
}
