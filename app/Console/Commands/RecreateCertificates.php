<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;
use DB;

class RecreateCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:recreatecertificate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recreate users certificates';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $certificatesUsers = DB::table('certificate_user')->get();

        $bar = $this->output->createProgressBar(count($certificatesUsers));

        $bar->start();
        foreach ($certificatesUsers as $certificateUser) {

            $user = User::find($certificateUser->user_id);

            $certificate = Certificate::find($certificateUser->certificate_id);

            $data = [
                'student_name' => $user->name,
                'security_code' => $certificateUser->validation_id,
                'issue_date' => $certificateUser->created_at,
                'cert_name' => $certificate->name,
                'cert_description' => $certificate->description
            ];

            $pdf = \App::make('snappy.pdf.wrapper');
            $pdf->loadView('pdf.certificate', $data)
                ->setPaper('a4')
                ->setOrientation('landscape')
                ->setOption('margin-bottom', 0)
                ->setOption('margin-left', 0)
                ->setOption('margin-right', 0)
                ->setOption('margin-top', 0);

            Storage::disk('s3')->put('user/certificate/' . $certificateUser->validation_id . ".pdf", $pdf->output(), 'public');

            $path = Storage::disk('s3')->url('user/certificate/' . $certificateUser->validation_id . ".pdf");

            DB::table('certificate_user')->where("id", $certificateUser->id)->update(["url" => $path]);

            $bar->advance();
        }
        $bar->finish();

        return 0;
    }
}
