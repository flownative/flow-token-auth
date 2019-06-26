<?php
namespace Flownative\TokenAuthentication\Security\Model;

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class HashAndRoles
{
    /**
     * @ORM\Id
     * @Flow\Identity
     * @var string
     */
    protected $hash;

    /**
     * @var string
     */
    protected $rolesHash;

    /**
     * @ORM\Column(type="json_array")
     * @var array
     */
    protected $roles = [];

    /**
     * @ORM\Column(type="json_array")
     * @var array
     */
    protected $settings = [];

    /**
     * @param string $hash
     * @param array $roles
     */
    public static function createWithHashAndRoles(string $hash, array $roles): HashAndRoles
    {
        $instance = new static();
        $instance->hash = $hash;
        $instance->roles = $roles;
        $instance->rolesHash = static::calculateHashForRoles($roles);

        return $instance;
    }

    /**
     * @param string $hash
     * @param array $roles
     * @param array $settings
     * @return HashAndRoles
     */
    public static function createWithHashRolesAndSettings(string $hash, array $roles, array $settings): HashAndRoles
    {
        $instance = static::createWithHashAndRoles($hash, $roles);
        $instance->settings = $settings;

        return $instance;
    }

    /**
     * @param string[] $roles
     * @return string
     */
    public static function calculateHashForRoles(array $roles): string
    {
        sort($roles);
        return sha1(json_encode($roles));
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }
}
