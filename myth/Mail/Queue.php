<?php

namespace Myth\Mail;

use Myth\Models\CIDbModel;

class Queue extends CIDbModel {

    protected $table_name = 'mail_queue';

    protected $set_created = false;
    protected $set_modified = false;
}