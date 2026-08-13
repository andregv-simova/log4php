<?php

/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at.
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * LoggerAppenderMail appends log events via email.
 *
 * This appender does not send individual emails for each logging requests but
 * will collect them in a buffer and send them all in a single email once the
 * appender is closed (i.e. when the script exists). Because of this, it may
 * not appropriate for long-running scripts, in which case
 * LoggerAppenderMailEvent might be a better choice.
 *
 * This appender uses a layout.
 *
 * ## Configurable parameters: ##
 *
 * - **to** - Email address(es) to which the log will be sent. Multiple email
 *     addresses may be specified by separating them with a comma.
 * - **from** - Email address which will be used in the From field.
 * - **subject** - Subject of the email message.
 *
 * @version $Revision$
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 *
 * @see http://logging.apache.org/log4php/docs/appenders/mail.html Appender documentation
 *
 * @note File changed by Joao M F Rebelo
 */
class LoggerAppenderMail extends LoggerAppender
{
    /**
     * Email address to put in From field of the email.
     */
    protected ?string $from = null;

    /**
     * The subject of the email.
     */
    protected string $subject = 'Log4php Report';

    /**
     * One or more comma separated email addresses to which to send the email.
     */
    protected ?string $to = null;

    /**
     * Indicates whether this appender should run in dry mode.
     *
     * @deprecated
     */
    protected bool $dry = false;

    /**
     * Buffer which holds the email contents before it is sent.
     */
    protected string $body = '';

    public function append(LoggerLoggingEvent $event)
    {
        if (null !== $this->layout) {
            $this->body .= $this->layout->format($event);
        }
    }

    public function close()
    {
        if (!$this->closed) {
            $from = $this->from;
            $to = $this->to;

            if (!empty($this->body) and null !== $from and null !== $to and null !== $this->layout) {
                $subject = $this->subject;
                if (!$this->dry) {
                    mail(
                        $to,
                        $subject,
                        $this->layout->getHeader().$this->body.$this->layout->getFooter(),
                        "From: {$from}\r\n"
                    );
                } else {
                    echo 'DRY MODE OF MAIL APP.: Send mail to: '.$to.' with content: '.$this->body;
                }
            }
            $this->closed = true;
        }
    }

    /** Sets the 'subject' parameter. */
    public function setSubject(mixed $subject)
    {
        $this->setString('subject', $subject);
    }

    /** Returns the 'subject' parameter. */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /** Sets the 'to' parameter. */
    public function setTo(mixed $to)
    {
        $this->setString('to', $to);
    }

    /** Returns the 'to' parameter. */
    public function getTo(): ?string
    {
        return $this->to;
    }

    /** Sets the 'from' parameter. */
    public function setFrom(mixed $from)
    {
        $this->setString('from', $from);
    }

    /** Returns the 'from' parameter. */
    public function getFrom(): ?string
    {
        return $this->from;
    }

    /** Enables or disables dry mode. */
    public function setDry(mixed $dry)
    {
        $this->setBoolean('dry', $dry);
    }
}
