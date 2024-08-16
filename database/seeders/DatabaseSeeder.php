<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /**
         * É necessário que as seeders sigam a ordem predefinida aqui
         * para não resultar em depedêcia de informações por parte de outras
         * seeders.
         */
        $this->call(PlansSeeder::class);
        $this->call(MailingListsSeeder::class);
        $this->call(TournamentSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(DevelopmentSeeder::class); // this one calls AdministratorSeeder inside it.
        $this->call(OperationStatusSeeder::class);
        $this->call(MachineSeeder::class);
        $this->call(OwnSeeder::class);
        $this->call(CommentsSeeder::class);
        $this->call(CourseSeeder::class);
        $this->call(AnsweredQuestionsSeeder::class);
        $this->call(TagSeeder::class);
        // preenche um perfil básico de pagamento no DB
       // $this->call(ShoppingSeeder::class);

        // cria os cupons de desconto dos usuários uhclabs e tropa
        //$this->call(DiscountSeeder::class);

        
    }
}
