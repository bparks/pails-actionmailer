<?php

namespace Pails\ActionMailer;

class Message
{
    private $to, $from, $subject, $view;
    private $transport;

    public function __construct($to, $from, $subject, $view, $transport)
    {
        $this->to = $to;
        $this->from = $from;
        $this->subject = $subject;
        $this->view = $view;
        $this->transport = $transport;
    }

    public function deliver()
    {
        $this->transport->deliver($this);
    }

    public function getTo()
    {
        return $this->to;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getContentTypeHeader()
    {
        $html_path = Mailer::pathFor($this->view.'.html.php');
        $text_path = Mailer::pathFor($this->view.'.text.php');

        if ($html_path != null && file_exists($html_path))
        {
            if ($text_path != null && file_exists($text_path))
            {
                return "multipart/alternative;boundary=----mail-boundary----";
            }
            return "text/html";
        }
        return "text/plain";
    }

    public function render()
    {
        $html = $this->renderHtml();
        $text = $this->renderText();

        $body = '';
        if ($text != null && $text != '' && $html != null && $html != '')
        {
            $body .= "------mail-boundary----\r\n";
            $body .= "Content-Type: text/plain\r\n";
            $body .= "Content-Transfer-Encoding: quoted-printable\r\n";
            $body .= quoted_printable_encode($text)."\r\n";
            $body .= "------mail-boundary----\r\n";
            $body .= "Content-Type: text/html\r\n";
            $body .= "Content-Transfer-Encoding: quoted-printable\r\n";
            $body .= quoted_printable_encode($html)."\r\n";
            $body .= "------mail-boundary------";
        }
        else
        {
            $body .= $html != null ? $html : $text;
        }
        if ($body == null || trim($body) == '')
            throw new \Exception("No content type templates for mailer view ".$this->view);
        return $body;
    }

    public function renderHtml()
    {
        $html_path = Mailer::pathFor($this->view.'.html.php');
        return $this->renderPath($html_path);
    }

    public function renderText()
    {
        $text_path = Mailer::pathFor($this->view.'.text.php');
        return $this->renderPath($text_path);
    }

    private function renderPath($path)
    {
        if (!file_exists($path))
            return null;

        ob_start();
        include $path;
        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }
}
