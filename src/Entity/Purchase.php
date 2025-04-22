<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ ORM\Entity( repositoryClass: PurchaseRepository::class ) ]

class Purchase {
    #[ ORM\Id ]
    #[ ORM\GeneratedValue ]
    #[ ORM\Column ]
    private ?int $id = null;

    #[ ORM\ManyToOne( inversedBy: 'purchases' ) ]
    private ?User $user = null;

    #[ ORM\Column ]
    private ?\DateTimeImmutable $purchaseDate = null;

    #[ ORM\Column( length: 255 ) ]
    private ?string $status = null;

    #[ ORM\Column( type: Types::DECIMAL, precision: 10, scale: 2 ) ]
    private ?string $totalAmount = null;

    #[ ORM\Column( type: Types::TEXT ) ]
    private ?string $shippingAddress = null;

    #[ ORM\Column( length: 255 ) ]
    private ?string $payementMethode = null;

    /**
     * @var Collection<int, PurchaseItem>
     */
    #[ORM\OneToMany(targetEntity: PurchaseItem::class, mappedBy: 'purchase')]
    private Collection $purchaseItems;

    public function __construct()
    {
        $this->purchaseItems = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser( ?User $user ): static {
        $this->user = $user;

        return $this;
    }

    public function getPurchaseDate(): ?\DateTimeImmutable {
        return $this->purchaseDate;
    }

    public function setPurchaseDate( \DateTimeImmutable $purchaseDate ): static {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    public function getStatus(): ?string {
        return $this->status;
    }

    public function setStatus( string $status ): static {
        $this->status = $status;

        return $this;
    }

    public function getTotalAmount(): ?string {
        return $this->totalAmount;
    }

    public function setTotalAmount( string $totalAmount ): static {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getShippingAddress(): ?string {
        return $this->shippingAddress;
    }

    public function setShippingAddress( string $shippingAddress ): static {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    public function getPayementMethode(): ?string {
        return $this->payementMethode;
    }

    public function setPayementMethode( string $payementMethode ): static {
        $this->payementMethode = $payementMethode;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseItem>
     */
    public function getPurchaseItems(): Collection
    {
        return $this->purchaseItems;
    }

    public function addPurchaseItem(PurchaseItem $purchaseItem): static
    {
        if (!$this->purchaseItems->contains($purchaseItem)) {
            $this->purchaseItems->add($purchaseItem);
            $purchaseItem->setPurchase($this);
        }

        return $this;
    }

    public function removePurchaseItem(PurchaseItem $purchaseItem): static
    {
        if ($this->purchaseItems->removeElement($purchaseItem)) {
            // set the owning side to null (unless already changed)
            if ($purchaseItem->getPurchase() === $this) {
                $purchaseItem->setPurchase(null);
            }
        }

        return $this;
    }

}
