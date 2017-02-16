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
 * Message
 *
 * @ORM\Table(name="awhs_crm_messages")
 * @ORM\Entity(repositoryClass="AWHS\CrmBundle\Entity\MessageRepository")
 */
class Message
{
    /**
     * @ORM\ManyToOne(targetEntity="AWHS\CrmBundle\Entity\Ticket", inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ticket;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AWHS\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var boolean
     *
     * @ORM\Column(name="clientReaded", type="boolean")
     */
    private $clientReaded = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="supportReaded", type="boolean")
     */
    private $supportReaded = 0;


    private $shortDate;

    public function __construct()
    {
        $this->date = new \DateTime();
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
     * Get sender
     *
     * @return \AWHS\UserBundle\Entity\User
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set sender
     *
     * @param \AWHS\UserBundle\Entity\User $sender
     * @return $this
     */
    public function setSender(\AWHS\UserBundle\Entity\User $sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Message
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return \AWHS\CrmBundle\Entity\Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Set ticket
     *
     * @param \AWHS\CrmBundle\Entity\Ticket $ticket
     * @return $this
     */
    public function setTicket(\AWHS\CrmBundle\Entity\Ticket $ticket)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get clientReaded
     *
     * @return boolean
     */
    public function getClientReaded()
    {
        return $this->clientReaded;
    }

    /**
     * Set clientReaded
     *
     * @param boolean $clientReaded
     * @return Message
     */
    public function setClientReaded($clientReaded)
    {
        $this->clientReaded = $clientReaded;

        return $this;
    }

    /**
     * Get supportReaded
     *
     * @return boolean
     */
    public function getSupportReaded()
    {
        return $this->supportReaded;
    }

    /**
     * Set supportReaded
     *
     * @param boolean $supportReaded
     * @return Message
     */
    public function setSupportReaded($supportReaded)
    {
        $this->supportReaded = $supportReaded;

        return $this;
    }

    /**
     * Get shortDate
     *
     * @return string
     */
    public function getShortDate()
    {
        if ($this->sortDate == null) {
            $this->shortDate = $this->get_timespan_string($this->getDate(), new \DateTime());
        }
        return $this->shortDate;
    }

    /**
     * Set shortDate
     * @return Message
     * @internal param string $shortDate
     */
    public function setShortDate()
    {
        $this->shortDate = $this->get_timespan_string($this->getDate(), new \DateTime());

        return $this;
    }

    /**
     * Source code from http://php.net/manual/fr/datetime.diff.php#107434
     */
    function get_timespan_string($older, $newer)
    {
        $Y1 = $older->format('Y');
        $Y2 = $newer->format('Y');
        $Y = $Y2 - $Y1;

        $m1 = $older->format('m');
        $m2 = $newer->format('m');
        $m = $m2 - $m1;

        $d1 = $older->format('d');
        $d2 = $newer->format('d');
        $d = $d2 - $d1;

        $H1 = $older->format('H');
        $H2 = $newer->format('H');
        $H = $H2 - $H1;

        $i1 = $older->format('i');
        $i2 = $newer->format('i');
        $i = $i2 - $i1;

        $s1 = $older->format('s');
        $s2 = $newer->format('s');
        $s = $s2 - $s1;

        if ($s < 0) {
            $i = $i - 1;
            $s = $s + 60;
        }
        if ($i < 0) {
            $H = $H - 1;
            $i = $i + 60;
        }
        if ($H < 0) {
            $d = $d - 1;
            $H = $H + 24;
        }
        if ($d < 0) {
            $m = $m - 1;
            $d = $d + $this->get_days_for_previous_month($m2, $Y2);
        }
        if ($m < 0) {
            $Y = $Y - 1;
            $m = $m + 12;
        }
        $timespan_string = $this->create_timespan_string($Y, $m, $d, $H, $i, $s);
        return $timespan_string;
    }

    /**
     * Source code from http://php.net/manual/fr/datetime.diff.php#107434
     */
    function get_days_for_previous_month($current_month, $current_year)
    {
        $previous_month = $current_month - 1;
        if ($current_month == 1) {
            $current_year = $current_year - 1; //going from January to previous December 
            $previous_month = 12;
        }
        if ($previous_month == 11 || $previous_month == 9 || $previous_month == 6 || $previous_month == 4) {
            return 30;
        } else if ($previous_month == 2) {
            if (($current_year % 4) == 0) { //remainder 0 for leap years 
                return 29;
            } else {
                return 28;
            }
        } else {
            return 31;
        }
    }

    /**
     * Source code from http://php.net/manual/fr/datetime.diff.php#107434
     */
    function create_timespan_string($Y, $m, $d, $H, $i, $s)
    {
        $timespan_string = '';
        $found_first_diff = false;
        if ($Y >= 1) {
            $found_first_diff = true;
            $timespan_string .= $this->pluralize($Y, 'year') . ' ';
            return $timespan_string;
        }
        if ($m >= 1 || $found_first_diff) {
            $found_first_diff = true;
            $timespan_string .= $this->pluralize($m, 'month') . ' ';
            return $timespan_string;
        }
        if ($d >= 1 || $found_first_diff) {
            $found_first_diff = true;
            $timespan_string .= $this->pluralize($d, 'day') . ' ';
            return $timespan_string;
        }
        if ($H >= 1 || $found_first_diff) {
            $found_first_diff = true;
            $timespan_string .= $this->pluralize($H, 'hour') . ' ';
            return $timespan_string;
        }
        if ($i >= 1 || $found_first_diff) {
            $found_first_diff = true;
            $timespan_string .= $this->pluralize($i, 'min') . ' ';
            return $timespan_string;
        }
        /*if($found_first_diff) { 
          $timespan_string .= 'and '; 
        } */
        $timespan_string .= $this->pluralize($s, 'second');
        return $timespan_string;
    }
    
    /**
     * Source code from http://php.net/manual/fr/datetime.diff.php#107434
     */
    function pluralize($count, $text)
    {
        return $count . (($count == 1) ? (" $text") : (" ${text}s"));
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Message
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }
}
