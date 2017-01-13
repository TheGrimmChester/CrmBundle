<?php

/**
 * Copyright (c) 2010-2017, AWHSPanel by Nicolas Méloni
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 *     * Neither the name of AWHSPanel nor the names of its contributors
 *       may be used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace AWHS\CrmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ticket
 *
 * @ORM\Table(name="awhs_crm_tickets")
 * @ORM\Entity(repositoryClass="AWHS\CrmBundle\Entity\TicketRepository")
 */
class Ticket
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AWHS\UserBundle\Entity\User", inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="AWHS\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="AWHS\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $assignedTo;

    /**
     * @ORM\ManyToOne(targetEntity="AWHS\CrmBundle\Entity\TicketPriority")
     * @ORM\JoinColumn(nullable=false)
     */
    private $priority;

    /**
     * @ORM\ManyToOne(targetEntity="AWHS\CrmBundle\Entity\Level")
     * @ORM\JoinColumn(nullable=true)
     */
    private $level;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="AWHS\CrmBundle\Entity\Message", mappedBy="ticket")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $messages;

    /**
     * @var boolean
     *
     * @ORM\Column(name="closed", type="boolean")
     */
    private $closed;

    /**
     * @var boolean
     *
     * @ORM\Column(name="resolved", type="boolean")
     */
    private $resolved;

    /**
     * @var string
     *
     * @ORM\Column(name="settings", type="string", length=1000)
     */
    private $settings;

    public function __construct()
    {
        $this->settings = "{}";
        $this->closed = 0;
        $this->resolved = 0;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get author
     *
     * @return AWHS\UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set author
     *
     * @param AWHS\UserBundle\Entity\User $author
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get client
     *
     * @return AWHS\UserBundle\Entity\User
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set client
     *
     * @param AWHS\UserBundle\Entity\User $client
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get assignedTo
     *
     * @return AWHS\UserBundle\Entity\User
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    /**
     * Set assignedTo
     *
     * @param AWHS\UserBundle\Entity\User $assignedTo
     * @return $this
     */
    public function setAssignedTo($assignedTo)
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Ticket
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Get closed
     *
     * @return boolean
     */
    public function getClosed()
    {
        return $this->closed;
    }

    /**
     * Set closed
     *
     * @param boolean $closed
     * @return Ticket
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;

        return $this;
    }

    /**
     * Get resolved
     *
     * @return boolean
     */
    public function getResolved()
    {
        return $this->resolved;
    }

    /**
     * Set resolved
     *
     * @param boolean $resolved
     * @return Ticket
     */
    public function setResolved($resolved)
    {
        $this->resolved = $resolved;

        return $this;
    }

    /**
     * Get priority
     *
     * @return \AWHS\CrmBundle\Entity\TicketPriority
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set priority
     *
     * @param \AWHS\CrmBundle\Entity\TicketPriority $priority
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get settings
     *
     * @return string
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Get settings
     *
     * @return string
     */
    public function getSettingsArray()
    {
        return json_decode($this->settings, true);
    }

    /**
     * Set settings
     *
     * @param string $settings
     * @return Ticket
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Get level
     *
     * @return AWHS\CrmBundle\Entity\Level
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param AWHS\CrmBundle\Entity\Level $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }
}
