<?php
use App\Models\User;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

beforeEach(function () {
    $this->user = new User();
});

test('test if use traits', function() {
    $traits = [
        HasApiTokens::class,
        HasFactory::class,
        Notifiable::class,
        HasRoles::class,
    ];
    $userTraits = array_keys(class_uses(User::class));
    expect($traits)->toBe($userTraits);
});

test('test fillable attribute', function () {
    $fillable = ['name', 'email', 'password'];
    expect($fillable)->toBe($this->user->getFillable());
});


test('test hidden attribute', function () {
    $hidden = ['password', 'remember_token'];
    expect($hidden)->toBe($this->user->getHidden());
});

test('test casts attribute', function () {
    $casts = ['id' => 'int','email_verified_at' => 'datetime'];
    expect($casts)->toBe($this->user->getCasts());
});

