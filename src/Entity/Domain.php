<?php

declare(strict_types=1);

namespace Redbitcz\SubregApi\Entity;

use DateTimeImmutable;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;
use Redbitcz\SubregApi\Context\Context;
use Redbitcz\SubregApi\Context\ContextAware;
use Redbitcz\SubregApi\Helpers;
use Redbitcz\SubregApi\Schema;
use Redbitcz\SubregApi\Schema\SchemaObject;

/**
 * ## Schema
 * - string    name    Domain name
 * - string    expire    Domain expiration date
 * - int    autorenew    Domain autorenew setting (0 - EXPIRE | 1 - AUTORENEW | 2 - RENEWONCE)
 */
class Domain
{
    use SchemaObject;
    use ContextAware;

    public const AUTORENEW_EXPIRE = 0;
    public const AUTORENEW_AUTORENEW = 1;
    public const AUTORENEW_RENEWONCE = 2;

    public function __construct(array $data, ?Context $context = null)
    {
        $this->setData($data);
        $this->setContext($context);
    }

    public function defineSchema(): Structure
    {
        return Expect::structure(
            [
                'name' => Expect::string()->required(),
                'expire' => (new Schema\Date())->required(),
                'autorenew' => Expect::anyOf(
                    self::AUTORENEW_EXPIRE,
                    self::AUTORENEW_AUTORENEW,
                    self::AUTORENEW_RENEWONCE
                )
                    ->before([Helpers::class, 'soapInt'])
                    ->required(),
            ]
        );
    }

    public function getName(): string
    {
        return $this->getMandatoryItem('name');
    }

    public function getExpire(): DateTimeImmutable
    {
        return $this->getMandatoryItem('expire');
    }

    public function getAutorenew(): int
    {
        return $this->getMandatoryItem('autorenew');
    }

    public function info(): DomainInfo
    {
        return $this->getMandatoryContext()->domain()->info($this->getName());
    }

    public function infoCz(): DomainInfoCz
    {
        return $this->getMandatoryContext()->domain()->infoCz($this->getName());
    }

    public static function fromResponseItem(array $data, ?Context $context = null): self
    {
        return new self($data, $context);
    }
}
