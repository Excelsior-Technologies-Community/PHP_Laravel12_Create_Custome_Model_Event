# PHP_Laravel12_Create_Custome_Model_Event

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel">
  <img src="https://img.shields.io/badge/Model-Events-blue?style=for-the-badge">
  <img src="https://img.shields.io/badge/Observer-Pattern-success?style=for-the-badge">
</p>

---

##  Overview  
This project demonstrates how to create a **custom model event called `activated`** in Laravel 12 using Observers.

When a product is activated:

✔ Status becomes “2”  
✔ `activated_at` timestamp updates  
✔ Custom event is fired  
✔ Observer automatically handles update  
✔ Log entry is created  

This workflow implements the **Model → Event → Observer** pattern.

---

#  Folder Structure  
```
app/
├── Models/
│   └── Product.php
├── Observers/
│   └── ProductObserver.php
└── Providers/
    └── EventServiceProvider.php

app/Http/Controllers/
└── ProductController.php

routes/
└── web.php

database/
└── migrations/
```

---

#  Step 1 — Install Laravel  

```bash
composer create-project laravel/laravel="^12" PHP_Laravel12_Create_Custome_Model_Event
cd PHP_Laravel12_Create_Custome_Model_Event
```

---

#  Step 2 — Update Database (.env)

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=custome_event
DB_USERNAME=root
DB_PASSWORD=
```

---

#  Step 3 — Create Migration  

Run Command:

```bash
php artisan make:model Product -m
```

Migration file:

```php
public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->integer('price');
        $table->tinyInteger('status')->default(0);
        $table->timestamp('activated_at')->nullable();
        $table->timestamps();
    });
}
```

Run migration:

```bash
php artisan migrate
```

---

#  Step 4 — Create Product Model with Custom Event  

 **app/Models/Product.php**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'status', 'activated_at'];

    protected $observables = ['activated'];

    public function makeActive()
    { 
        $this->update(['status' => 2]);

        // Trigger the custom event
        $this->fireModelEvent('activated', false);
    }
}
```

---

#  Step 5 — Create Observer  

Run:

```bash
php artisan make:observer ProductObserver --model=Product
```

 **app/Observers/ProductObserver.php**

```php
<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function activated(Product $product)
    {
        \Log::info("🔥 OBSERVER FIRED for product ID: {$product->id}");

        $product->activated_at = now();
        $product->save();
    }
}
```

---

#  Step 6 — Register Observer  

 **app/Providers/EventServiceProvider.php**

```php
use App\Models\Product;
use App\Observers\ProductObserver;

public function boot()
{
    Product::observe(ProductObserver::class);
}
```

---

#  Step 7 — Create Controller  

 **app/Http/Controllers/ProductController.php**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::find(1);

        $product->makeActive();

        dd($product);
    }
}
```

---

#  Step 8 — Create Route  

 **routes/web.php**

```php
use App\Http\Controllers\ProductController;

Route::get('/test-activate', [ProductController::class, 'index']);
```

---

#  Step 9 — Insert Test Data  

```bash
php artisan tinker
```

```php
Product::create(['name' => 'Laptop', 'price' => 50000]);
Product::create(['name' => 'Mobile', 'price' => 10000]);
```

---

#  Step 10 — Test Custom Event  

Visit:

```
http://127.0.0.1:8000/test-activate
```

### You Will See:
<img width="482" height="937" alt="Screenshot 2025-12-11 122038" src="https://github.com/user-attachments/assets/6caa45ff-7657-41da-8e85-1e8ad3198be6" />


✔ `status = 2`  
✔ `activated_at` updated  
✔ Log entry created:  
```
 OBSERVER FIRED for product ID: 1
```  
✔ Dump of updated product  

---

#  Final Result  

You now have a fully working Custom Model Event System:

✔ Custom `activated` event  
✔ Observer updates timestamp  
✔ Product activation workflow  
✔ Log tracking  
✔ Clean and scalable structure  


