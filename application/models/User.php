<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends EloquentModel
{
    // 软删除
    use SoftDeletes;

    // 表名
    public $table = 'users';

    // 此字段自动转换成 Carbon 实例
    protected $dates = ['deleted_at'];

    // 允许批量赋值的字段
    protected $fillable = ['username', 'password', 'email'];
}