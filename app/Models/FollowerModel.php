<?php

namespace App\Models;

use CodeIgniter\Model;

class FollowerModel extends Model
{
    protected $table = 'followers';
    protected $primaryKey = 'id';
    protected $allowedFields = ['follower_id', 'following_id', 'created_at'];
}
?>