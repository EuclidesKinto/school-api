<?php

namespace App\Http\Controllers\Api\Userland;

use App\Http\Controllers\Controller;
use App\Http\Resources\Collections\CertificationMachineCollection;
use App\Http\Resources\Userland\CertificationStatusResource;
use App\Models\Certification;
use App\Models\CertificationUser;
use App\Models\Course;
use App\Models\Machine;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class CertificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function askForCertification(Certification $certification)
    {
        $user = Auth::user();

        $certificationUser = DB::table('certification_user')->where([['user_id', $user->id], ['certification_id', $certification->id],])->latest()->first();

        if ($certificationUser) {

            if ($certificationUser->approved) {
                return response()->json(['message' => 'usuário já fez a certificação e já passou'], 403);
            }

            if ($certificationUser->timeout == null) {
                return response()->json(['message' => 'usuário só pode fazer uma certificação por vez'], 403);
            }

            $timeout = new Carbon($certificationUser->timeout);

            if ($timeout->isFuture()) {

                return response()->json(['message' => 'Somente depois de transcorridos seis meses a partir da data de reprovação, o usuário poderá fazer a certificação.'], 403);
            }
        }

        if ($certification->course_id == null) {

            if (DB::table('users_allowed_certifications')->where('user_id', $user->id)->where('certification_id', $certification->id)->exists()) {

                $machines = Machine::where([['type', 'certification'], ['certification_id', $certification->id]])
                    ->inRandomOrder()
                    ->limit($certification->machines_number)
                    ->get();

                if ($machines->isEmpty() || count($machines) < $certification->machines_number) {

                    return response()->json(['message' => 'certificação não possui numero de maquinas minimas para iniciar.'], 403);
                }

                $id = DB::table('certification_user')->insertGetId([
                    'user_id' => $user->id,
                    'certification_id' => $certification->id,
                    'current_step' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                foreach ($machines as $machine) {

                    $machine->certificationUser()->attach($id);
                }

                return response()->json(['message' => 'Certificação iniciada, boa sorte!'], 200);
            } else {

                //vai pro billing pro user poder pagar pela certificacao
                return 0;
            }
        } else {

            if ($user->is_premium()) {

                $course = Course::find($certification->course_id);

                $challengePercentage = $course->getChallengeCompletionPercentage($course->id);

                $coursePercentage = $course->getCourseCompletionPercentage($course->id);

                if ($challengePercentage >= $course->percentage_challenges && $coursePercentage >= $course->percentage_course) {

                    $machines = Machine::where([['type', 'certification'], ['certification_id', $certification->id]])
                        ->inRandomOrder()
                        ->limit($certification->machines_number)
                        ->get();

                    if ($machines->isEmpty() || count($machines) < $certification->machines_number) {

                        return response()->json(['message' => 'certificação não possui numero de maquinas minimas para iniciar.'], 403);
                    }

                    $id = DB::table('certification_user')->insertGetId([
                        'user_id' => $user->id,
                        'certification_id' => $certification->id,
                        'current_step' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    foreach ($machines as $machine) {

                        $machine->certificationUser()->attach($id);
                    }

                    return response()->json(['message' => 'Certificação iniciada, boa sorte!'], 200);
                } else {

                    return response()->json(['message' => 'usuário não possui os pré requisitos.'], 403);
                }
            } else {

                return response()->json(['message' => 'usuário não assinante.'], 403);
            }
        }
    }

    public function startDeadline(Certification $certification)
    {

        $user = Auth::user();

        $deadline = Carbon::now()->addDays($certification->deadline_in_days);

        try {

            $certificationInfo = CertificationUser::where([['user_id', $user->id], ['certification_id', $certification->id]])->latest()->first();

            $machines = $certificationInfo->machines()->get();

            DB::table('certification_user')
                ->where([['user_id', $user->id], ['certification_id', $certification->id]])
                ->latest()->limit(1)
                ->update(['deadline' => $deadline, 'current_step'=> 2]);

            $instancesController = new InstancesController;

            foreach ($machines as $machine) {

                $instancesController->deployCertification($machine, $deadline);
            }
        } catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => 'algo deu errado ao iniciar as instancias.',
                500
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Prazo e maquinas iniciados',
            200
        ]);
    }

    public function check(Certification $certification)
    {
        $user = Auth::user();

        $certificationUser = DB::table('certification_user')->where([['user_id', $user->id], ['certification_id', $certification->id],])->latest()->first();

        if ($certificationUser) {

            if ($certificationUser->approved) {
                return response()->json(['message' => 'usuário já fez a certificação e já passou'], 403);
            }

            if ($certificationUser->timeout == null) {
                return response()->json(['message' => 'usuário só pode fazer uma certificação por vez'], 403);
            }

            $timeout = new Carbon($certificationUser->timeout);

            if ($timeout->isFuture()) {

                return response()->json(['message' => 'Somente depois de transcorridos seis meses a partir da data de reprovação, o usuário poderá fazer a certificação.'], 403);
            }
        }

        $machines = Machine::where([['type', 'certification'], ['certification_id', $certification->id]])
            ->inRandomOrder()
            ->limit($certification->machines_number)
            ->get();

        if ($machines->isEmpty() || count($machines) < $certification->machines_number) {

            return response()->json(['message' => 'certificação não possui numero de maquinas minimas para iniciar.'], 403);
        }

        if ($certification->course_id == null && DB::table('users_allowed_certifications')->where('user_id', $user->id)->where('certification_id', $certification->id)->exists()) {
            return response()->json(['message' => 'Usuário pode iniciar certificação'], 200);
        }

        if (!$user->is_premium()) {
            return response()->json(['message' => 'usuário não assinante.'], 403);
        }

        $course = Course::find($certification->course_id);

        $challengePercentage = $course->getChallengeCompletionPercentage($course->id);

        $coursePercentage = $course->getCourseCompletionPercentage($course->id);

        if ($challengePercentage < $course->percentage_challenges || $coursePercentage < $course->percentage_course) {

            return response()->json(['message' => 'usuário não possui os pré requisitos.'], 403);
        }

        return response()->json(['message' => 'Usuário pode iniciar certificação'], 200);
    }

    public function status(Certification $certification)
    {
        $user = Auth::user();

        $certificationInfo = DB::table('certification_user')
            ->where([['user_id', $user->id], ['certification_id', $certification->id]])->latest()->first();

        if ($certificationInfo == null) {
            abort(403, 'certificado ainda não iniciado');
        }

        return new CertificationStatusResource($certificationInfo);
    }

    public function timeLeft(Certification $certification)
    {

        $this->checkCertification($certification);

        $user = Auth::user();

        $certificationInfo = DB::table('certification_user')->where([['user_id', $user->id], ['certification_id', $certification->id]])->latest()->first();

        $deadline = new Carbon($certificationInfo->deadline);

        return CarbonInterval::seconds($deadline->diffInSeconds())->cascade()->forHumans() . ' UTC';
    }

    public function sendReport(Request $request, Certification $certification)
    {
        $this->checkCertification($certification);

        $user = Auth::user();

        $file = $request->file('report');
        $fileName =  $user->name . "RelatorioPentest" . Carbon::now()->isoFormat('DDMMYYYY');

        if (env('APP_ENV') === 'local') {
            Storage::disk('public')->put('user/certification/reports/' . $fileName . ".pdf", $file);
            $path = Storage::disk('public')->url('user/certification/reports/' . $fileName . ".pdf");
        } else {
            Storage::disk('s3')->put('user/certification/reports/' . $fileName . ".pdf", $file, 'public');
            $path = Storage::disk('s3')->url('user/certification/reports/' . $fileName . ".pdf");
        }

        DB::table('certification_user')
            ->where([['user_id', $user->id], ['certification_id', $certification->id]])
            ->latest()->limit(1)
            ->update(['user_report' => $path, 'current_step'=> 4]);

        $instancesController = new InstancesController;

        $certificationInfo = CertificationUser::where([['user_id', $user->id], ['certification_id', $certification->id]])->latest()->first();

        foreach ($certificationInfo->machines()->get() as $machine) {

            $content = new Request();

            $instancesController->terminate($content, $machine->id);
        }

        return response()->json(['message' => 'Relatório enviado e máquinas encerradas, aguarde a avaliação técnica para saber sua nota, boa sorte!'], 200);
    }

    public function indexMachines(Certification $certification)
    {

        $this->checkCertification($certification);

        $user = Auth::user();

        $certificationInfo = CertificationUser::where([['user_id', $user->id], ['certification_id', $certification->id]])->latest()->first();

        return CertificationMachineCollection::collection($certificationInfo->machines()->with('flags')->get());
    }

    public function pownMachine(Request $request, Machine $machine)
    {

        $user = Auth::user();

        $flag = trim($request->input('flag'));

        $flagController = new FlagController;

        return $flagController->own($flag, $machine);
    }

    public function download(Certification $certification)
    {

        $user = Auth::user();

        $certificationInfo = CertificationUser::where([['user_id', $user->id], ['certification_id', $certification->id]])->latest()->first();

//        if ($certificationInfo->validation_id) {
//            abort(403, 'certificado ja foi gerado');
//        }

        if ($certificationInfo == null) {
            abort(403, 'certificado ainda não iniciado');
        }

        if (!$certificationInfo->approved) {
            abort(403, 'usuário não foi aprovado');
        }

        $uuid = Str::uuid();

        $data = [
            'student_name' => $user->name,
            'security_code' => $uuid,
            'issue_date' => Carbon::now()->format('d/m/y'),
            'cert_name' => $certificationInfo->certification->name,
            'cert_description' => $certificationInfo->certification->description
        ];

        $pdf = \App::make('snappy.pdf.wrapper');
        $pdf->loadView('pdf.certification', $data)
            ->setPaper('a4')
            ->setOrientation('landscape')
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0)
            ->setOption('margin-top', 0);

        Storage::disk('s3')->put('user/certification/pdf/' . $uuid . ".pdf", $pdf->output(), 'public');

        $path = Storage::disk('s3')->url('user/certification/pdf/' . $uuid . ".pdf");

        $certificationInfo->update(['url' => $path, 'validation_id' => $uuid]);

        return $pdf->download($uuid . ".pdf");
    }

    public function startMachine(Certification $certification, Machine $machine)
    {
        $this->checkCertification($certification);

        $user = Auth::user();

        $certificationInfo = CertificationUser::where([['user_id', $user->id], ['certification_id', $certification->id]])->latest()->first();

        $instancesController = new InstancesController;

        $deadline = new Carbon($certificationInfo->deadline);

        $instancesController->deployCertification($machine, $deadline);

        return response()->json(['message' => 'Maquina iniciada com sucesso'], 200);
    }

    public function checkCertification($certification)
    {
        $user = Auth::user();

        $certificationInfo = DB::table('certification_user')->where([['user_id', $user->id], ['certification_id', $certification->id]])->latest()->first();

        if ($certificationInfo == null) {
            abort(403, 'certificado ainda não iniciado');
        }

        if ($certificationInfo->user_report != null && $certificationInfo->user_report != 'waiting_pdf') {
            abort(403, 'Relatório já enviado, aguarde a avaliação');
        }

//        $deadline = new Carbon($certificationInfo->deadline);
//
//        if ($deadline->isPast()) {
//            abort(403, 'O prazo acabou no dia: ' . $deadline . ' UTC');
//        }
    }


    /**
     * @param Certification $certification
     * @return JsonResponse
     */
    public function startSendReport(Certification $certification): JsonResponse
    {
        try {
            $this->checkCertification($certification);

            $user = Auth::user();
            $deadline = Carbon::now()->addDays($certification->deadline_in_days);

            DB::transaction(function () use ($user, $certification, $deadline) {
                DB::table('certification_user')
                    ->where([['user_id', $user->id], ['certification_id', $certification->id]])
                    ->latest()->limit(1)
                    ->update([
                        'deadline_send_report' => $deadline,
                        'user_report' => 'waiting_pdf',
                        'current_step'=> 3
                    ]);
            });

            return response()->json(
                ['message' => 'O sistema agora irá aguardar o upload do PDF'],
                ResponseAlias::HTTP_OK
            );
        } catch (Exception $e) {
            Log::error('Erro ao redirecionar para pagina de envio do relatorio', ['exception' => $e]);
            return response()->json(
                ['error' => 'Houve um redirecionar para pagina de envio do relatório'],
                ResponseAlias::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
    public function certificationDisapproved(Certification $certification): JsonResponse
    {
        try {
            $this->checkCertification($certification);

            $user = Auth::user();

            DB::transaction(function () use ($user, $certification) {
                $timeout = Carbon::now()->addMonths(6);
                DB::table('certification_user')
                    ->where([['user_id', $user->id], ['certification_id', $certification->id]])
                    ->latest()->limit(1)
                    ->update([
                        'user_report' => 'waiting_pdf',
                        'approved' => 0,
                        'grade' => 0,
                        'comment' => 'user disapproved',
                        'url' => 'url',
                        'timeout' => $timeout,
                        'current_step'=> 4
                    ]);
            });

            return response()->json(
                ['message' => 'O aluno foi reprovado automaticamente por não ter respondido o relatório'],
                ResponseAlias::HTTP_OK
            );
        } catch (Exception $e) {
            Log::error('Erro ao reprovar o certificado', ['exception' => $e]);
            return response()->json(
                ['error' => 'Houve ao reprovar o certificado'],
                ResponseAlias::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

}

