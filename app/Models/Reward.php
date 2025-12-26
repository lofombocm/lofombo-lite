<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $table = 'rewards';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'nature', 'value', 'level', 'active', 'registered_by', 'image'];

    public static function generate_random_hex_color() {
        // Generate a random number between 0x000000 and 0xFFFFFF (0 and 16777215)
        $rand_int = mt_rand(0, 0xFFFFFF); // 0xFFFFFF is a hex literal

        // Convert the random integer to a hexadecimal string
        $hex_color = dechex($rand_int);

        // Pad the hex string with leading zeros to ensure it's always 6 characters long
        $padded_hex_color = str_pad($hex_color, 6, '0', STR_PAD_LEFT);

        // Prepend the '#' symbol to make it a valid HTML hex color code
        return '#' . $padded_hex_color;
    }
}
