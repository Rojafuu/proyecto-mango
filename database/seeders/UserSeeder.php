<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
  
       //  Administrador
        $admin = User::firstOrCreate(
            ['email' => 'administrador@mango.cl'],
            [
                'name' => 'Daniela Fuentes Escobar',
                'password' => Hash::make('12345678'),
                'rol' => 'administrador',
                'estado' => true,
            ]
        );

        $admin->rol = 'administrador';
        $admin->estado = true;
        $admin->save();

        $admin->syncRoles(['administrador']);

 
        // Profesional tatuador
        $prof = User::firstOrCreate(
            ['email' => 'tattoo@mango.cl'],
            [
                'name' => 'Yacin Pino Valenzuela',
                'password' => Hash::make('12345678'),
                'rol' => 'profesional',
                'estado' => true,
            ]
        );

        $prof->rol = 'profesional';
        $prof->estado = true;
        $prof->save();

        $prof->syncRoles(['profesional']);

        // Perfil profesional obligatorio (1:1)
        $prof->profesional()->updateOrCreate(
            ['user_id' => $prof->id],
            [
                'especialidad' => 'tatuaje',
                'anios_experiencia' => 5,
                'estado' => true,
            ]
        );


        // Profesional anilladora (bodypiercing)
        $pro3 = User::firstOrCreate(
            ['email' => 'piercing@mango.cl'],
            [
                'name' => 'Mastani',
                'password' => Hash::make('12345678'),
                'rol' => 'profesional',
                'estado' => true,
            ]
        );

        $pro3->rol = 'profesional';
        $pro3->estado = true;
        $pro3->save();

        $pro3->syncRoles(['profesional']);

        // Perfil profesional obligatorio (1:1) 
        $pro3->profesional()->updateOrCreate(
            ['user_id' => $pro3->id],
            [
                'especialidad' => 'bodypiercing',
                'anios_experiencia' => 4,
                'estado' => true,
            ]
        );
   

        // Cliente (perfil obligatorio 1:1)
        $cli = User::firstOrCreate(
            ['email' => 'cliente@mango.cl'],
            [
                'name' => 'Cliente Mango',
                'password' => Hash::make('12345678'),
                'rol' => 'cliente',
                'estado' => true,
            ]
        );

        $cli->rol = 'cliente';
        $cli->estado = true;
        $cli->save();

        $cli->syncRoles(['cliente']);

        // Perfil cliente obligatorio (1:1)
        $cli->cliente()->updateOrCreate(
            ['user_id' => $cli->id],
            [
                'fecha_nac' => null,
                'telefono' => null,
                'observaciones' => null,
            ]
        );
     }

    
}
