<?php

namespace App\Models;

use App\Scopes\ActiveScope;
use App\Services\Pagarme\Facades\Pagarme;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PagarmeCoreApiLib\Models\UpdatePlanItemRequest;
use PagarmeCoreApiLib\Models\UpdatePricingSchemeRequest;
use Illuminate\Support\Facades\Log;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ActiveScope);
    }

    protected $fillable = [
        'name',
        'description',
        'price',
        'details',
        'is_active',
        'productable_id',
        'productable_type',
        'productable_version'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'details' => AsArrayObject::class,
        'is_active' => 'bool'
    ];

    /**
     * Default values for product attributes
     */
    protected $attributes = [
        'details' => '{}'
    ];

    /**
     * get the parent productable model (Plan, etc.)
     */
    public function productable()
    {
        return $this->morphTo();
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class);
    }

    /**
     * Retorna apenas produtos que são gerados
     * por planos de assinaturas.
     */
    public function scopeOfPlans($query)
    {
        return $query->where('productable_type', Plan::class);
    }

    /**
     * Retorna o valor do produto em centavos
     * 
     * @return int $cents_price
     */
    public function getCentsPriceAttribute()
    {
        return (int) bcmul($this->price, 100);
    }

    /**
     * Retorna as informações deste produto
     * na pagarme
     */
    public function getOnPagarme()
    {
        $api = Pagarme::plans();
        try {
            $plan = $api->getPlanItem($this->productable->pagarme_plan_id, $this->details['pagarme_id']);
            return $plan;
        } catch (\Exception $th) {
            Log::error("pagarme::Erro ao buscar item do plano na pagarme: ", ['context' => json_encode($th)]);
        }
    }


    /**
     * Atualiza as configurações do produto na pagarme
     */
    public function updateOnPagarme()
    {
        /**
         * Se o produto for atrelado a um plano
         * Então precisamos pegar o 'pagarme_plan_id' do
         * Plan antes de tentar editar lá na pagarme
         */
        if ($this->productable instanceof Plan && !empty($this->details['pagarme_id'])) {
            $api = Pagarme::plans();
            $planItem = new UpdatePlanItemRequest();
            $planItem->name = $this->name;
            $planItem->description = $this->description;
            $planItem->quantity = 1;
            $planItem->status = $this->is_active ? 'active' : 'inactive';
            $planItem->pricingScheme = new UpdatePricingSchemeRequest();
            $planItem->pricingScheme->price = $this->cents_price;

            /**
             * Atualiza as informações lá na pagarme
             */
            try {
                $planItem = $api->updatePlanItem($this->productable->pagarme_plan_id, $this->details['pagarme_id'], $planItem);
                Log::debug("pagarme::Item do plano atualizado.", ['planItem' => json_encode($planItem)]);
                return $planItem;
            } catch (\Exception $th) {
                Log::error("pagarme::Erro ao atualizar item do plano.", ['planItem' => json_encode($planItem), 'error' => json_encode($th)]);
            }
        }
    }

    /**
     * Deleta o item do plano (produto)
     * Lá na pagarme
     */
    public function deleteOnPagarme()
    {
        if (!empty($this->details['pagarme_id'])) {
            try {
                $planItem = Pagarme::plans()->deletePlanItem($this->productable->pagarme_plan_id, $this->details['pagarme_id']);
                $this->details['pagarme_id'] = NULL;
                Log::debug("pagarme::Item do Plano Deletado: ", ['planItem' => json_encode($planItem), 'product' => $this->toJson()]);
            } catch (\Exception $th) {
                Log::error("pagarme::Erro ao deletar item do plano:", ['context' => json_encode($th)]);
            }
        }
    }
}
