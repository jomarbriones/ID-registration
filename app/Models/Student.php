<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // If your table name is not the default 'students', set it:
    protected $table = 'students'; // <-- change if needed, e.g. 'student_info'

    // If your primary key is not 'id', set it:
    // protected $primaryKey = 'student_id';

    // If your PK is not auto-incrementing or not integer:
    // public $incrementing = false;
    // protected $keyType = 'string';

    protected $fillable = [
        'id_number','first_name','middle_initial','last_name','course','blood_type',
        'address','guardian_name','parent_address','guardian_contact','picture_path','status','gender'
    ];

    // If your timestamps columns differ or are absent:
    // public $timestamps = true;
    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';

    
}
