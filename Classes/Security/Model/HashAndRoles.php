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
    protected string $hash;

    /**
     * @var string
     */
    protected string $rolesHash;

    /**
     * @ORM\Column(type="flow_json_array")
     * @var array
     */
    protected array $roles = [];

    /**
     * @ORM\Column(type="flow_json_array")
     * @var array
     */
    protected array $settings = [];

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    protected ?string $label = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime|null
     */
    protected ?\DateTime $expiresAt = null;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     * @var \DateTime
     */
    protected \DateTime $createdAt;

    /**
     * Creates a new instance of HashAndRoles.
     *
     * @param string $hash
     * @param array $roles
     * @param array $settings
     * @param string|null $label
     * @param \DateTime|null $expiresAt
     * @return HashAndRoles
     */
    public static function create(
        string $hash,
        array $roles,
        array $settings = [],
        ?string $label = null,
        ?\DateTime $expiresAt = null
    ): HashAndRoles
    {
        $instance = new static();
        $instance->createdAt = new \DateTime();
        $instance->hash = $hash;
        $instance->roles = $roles;
        $instance->rolesHash = static::calculateHashForRoles($roles);
        $instance->settings = $settings;
        $instance->label = $label ?? 'token-' . $instance->createdAt->format('Y-m-d-H-i-s');
        $instance->expiresAt = $expiresAt;

        return $instance;
    }

    /**
     * @param string $hash
     * @param array $roles
     * @return HashAndRoles
     * @deprecated Use create instead
     */
    public static function createWithHashAndRoles(string $hash, array $roles): HashAndRoles
    {
        return static::create(
            $hash,
            $roles,
        );
    }

    /**
     * @param string $hash
     * @param array $roles
     * @param array $settings
     * @return HashAndRoles
     *
     * @deprecated Use create instead
     */
    public static function createWithHashRolesAndSettings(string $hash, array $roles, array $settings): HashAndRoles
    {
        return static::create(
            $hash,
            $roles,
            $settings,
        );
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

    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
