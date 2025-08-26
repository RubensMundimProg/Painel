<?php

namespace Estrutura\Validator;

class EmailAddress extends \Zend\Validator\EmailAddress
{
    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID            => "O %s informado é inválido",
        self::INVALID_FORMAT     => "O %s informado é inválido",
        self::INVALID_HOSTNAME   => "O %s informado é inválido",
        self::INVALID_MX_RECORD  => "O %s informado é inválido",
        self::INVALID_SEGMENT    => "O %s informado é inválido",
        self::DOT_ATOM           => "O %s informado é inválido",
        self::QUOTED_STRING      => "O %s informado é inválido",
        self::INVALID_LOCAL_PART => "O %s informado é inválido",
        self::LENGTH_EXCEEDED    => "O %s informado é inválido",
    );

}
