<?php

namespace App\Models;

use Bavix\Wallet\Models\Wallet as ModelsWallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Wallet extends ModelsWallet
{
    use HasFactory;

    public function user()
{
    return $this->belongsTo(User::class, 'holder_id'); // Define the owner of this wallet
}

}