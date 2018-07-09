<?php

use Illuminate\Database\Eloquent\SoftDeletes;
use PHPCasts\Yaf\Databases\RelationDb as Model;

/**
 * 使用基于Laravel的Eloquent
 *
 * Class UserModel
 */
class UserModel extends Model
{
    // 软删除
    use SoftDeletes;

    // 表名
    public $table = 'users';

    // 此字段自动转换成 Carbon 实例
    protected $dates = ['deleted_at'];

    // 允许批量赋值的字段
    protected $fillable = ['name', 'password', 'email'];
}