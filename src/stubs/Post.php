<?php

namespace App;

use Jeremytubbs\Igor\Models\Post as IgorPost;

class Post extends IgorPost
{
    use \Jeremytubbs\Igor\Traits\SluggerTrait;

}
