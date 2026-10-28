<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualAccount extends Model
{
    protected $table = 'virtual_account';
    protected $primaryKey = 'id_va';
    protected $guarded = [];

    public function siswa() { return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa'); }
}
