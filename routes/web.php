<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $posts = Post::where('id', '>=', '20')
        ->orderBy('id', 'desc')
        ->take(3)
        ->get();

    foreach ($posts as $post) {
        echo "$post->id $post->title <br>";
    }
});
/**
 * Lo que realizamos completo fue lo siguiente:
 * 1- Utilizamos php artisan make:model Post -f -m
 * 2-Luego nos dirigimos a el archivo database/migrations/*create_post...
 * 3- Dentro de la funcion up se encuentra el Schema que modificaremos
 *    el cual representa nuestra tabla
 * 4- Luego ejecutamos "php artisan migrate" para crear las tablas
 * 5- Luego para trabajar con los dato, para eso
 *    nos dirigimos a database/fatories/PostFactory
 * 6- Allí colocamos en el return los datos que tiene que crear.
 * 7- Luego creamos datos utilizando:
 *    "php artisan tinker" esto abre la consola y ejecutamos:
 *    "Posts::factory()->count(30)->create()"
 * 8- A partir de allí si podemos utilizar los datos
 *    y mostrarlos en nuestra vista.
 */
Route::get('posts', function () {
    $posts = Post::get();

    foreach ($posts as $post) {
        dd($post->get_title);
        echo "
        $post->id
        <strong>{$post->user->get_name}</strong>
        $post->get_title
        <br>";
    }
    //Nota 1 (get_name, get_title)
});
Route::get('users', function () {
    $users = User::get();

    foreach ($users as $user) {
        echo "
        $user->id
        <strong>{$user->get_name}</strong>
        | {$user->posts->count()} posts
        <br>";
    }
    //Nota 1 (get_name)
});
/**
 * Lo que hicimos arriba es relacionar la tabla de posts con la de users
 * a travez de un id user_id
 * 1- Vamos al archivo database/migrations/*_create_post
 * 2- Creamos en el esquema unas nuemas columnas como:
 *    user_id y le indicamos que es una foreignkey del id de users
 * 3- Luego le dimos "php artisan migrate:refresh", sin embargo
 *    esto hizo que se borrara nuestros datos, asi que...
 * 4- Nos dirigimos al archivo database/seeds/DatabaseSeeder...
 * 5- Alli ejecutamos los comandos de creater con el factory como
 *    haciamos en el tinker
 * 6- Tenemos que actualizar los factorys como el
 *    Post factory agregandole el user_id
 * 7- Ahora ejecutamos el comando
 *    "php artisan migrate:refresh --seed"
 * 8- Ahora necesitamos especificar la relacion en los modelos:
 * 8.1-  Agregagamos un metodo posts al modelo User
 *       alli le indicamos que el puede tener muchos posts
 * 8.2-  Agregagamos un metodo users al modelo Post
 *       alli le indicamos que el pertenece a un Usuario
 */
Route::get('collections', function () {
    $users = User::all();
    // dd($users); // Podemos ver toda la coleccion
    // dd($users->contains(4)); // Podemos ver si la coleccion contiene el id 4
    // dd($users->contains(4)); // Podemos ver si la coleccion contiene el id 5 (no existe)
    //dd($users->except([1, 2, 3])); // Nos muestra todos menos los ids del array
    //dd($users->only([2, 3])); // Nos muestra solo los ids del array
    //dd($users->find(4)); // Nos busca el usuario de id 4
    dd($users->load('posts')); // Nos trae las relaciones que tiene este usuario con los posts
});

Route::get('serialization', function () {
    $users = User::all();
    //dd($users->toArray()); // Si queremos que nos devuelva un array
    $user = $users->find(1);
    //dd($user); // Si queremos los datos de solo un usuario
    dd($user->toJson()); // Si queremos un json de los datos
});
/**
 * Las colecciones son una pequeña extension de eloquent
 * que te permite manipular facilmente los datos
 * y Serializacion es la forma como presentamos nuestros datos
 * ya sea en array o json
 */

 /*
    Nota 1
  el get_name y el get_title se utliliza gracias a
  lo metodos getNameAttribute y getTitleAttribute
  que se encuentran en app/models/User.php
  y app/models/Posts, respectivamente. En ellos podemos
  indicar como queremos que se devuelvan los datos si
  colocamos un get_<atributo> como en estos ejemplos que
  no retornan los datos convertidos en minusculas.

  Además, se crearon los metodos setNameAttribute y
  setTitleAttribue para que se guarden de una forma
  especifica, en este caso en minusculas

  Si te fijas, get es para cambiar la forma en la
  que se devuelven los datos, y set para cambiar la
  forma en la que se guardan en la base de datos
  */
