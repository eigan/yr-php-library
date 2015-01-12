<?php

namespace Yr;

/**
 * Textual forecasts for a given day (or two days)
 * Note: There is some html inside the getText()...
 * Note: These are always norwegian
 *
 * @package Yr
 * @author Einar Gangsø <einargangso@gmail.com>
 */
class TextualForecast
{
    /**
     * @var String
     */
    protected $title;

    /**
     * @var String
     */
    protected $text;

    /**
     * @var \Datetime
     */
    protected $from;

    /**
     * @var \Datetime
     */
    protected $to;

    /**
     * @var String
     */
    const XML_DATE_FORMAT = "Y-m-d";

    /**
     * @param  String                    $title
     * @param  String                    $text
     * @param  \DateTime                 $from
     * @param  \DateTime                 $to
     * @throws \InvalidArgumentException
     */
    public function __construct($title, $text, \DateTime $from, \DateTime $to = null)
    {
        if (empty($title) || empty($text)) {
            throw new \InvalidArgumentException("Title/or text is empty");
        }

        $this->title = $title;
        $this->text  = $text;
        $this->from  = $from;
        $this->to    = $to === null ? $from : $to;
    }

    /**
     * @param  \SimpleXMLElement         $xml
     * @return TextualForecast
     * @throws \InvalidArgumentException
     */
    public static function createTextualForecastFromXml(\SimpleXMLElement $xml)
    {
        $data = Yr::xmlToArray($xml);

        $title = $data['title'];
        $text = $data['body'];
        $from = \DateTime::createFromFormat(TextualForecast::XML_DATE_FORMAT, $data['from']);
        $to = \DateTime::createFromFormat(TextualForecast::XML_DATE_FORMAT, $data['to']);

        return new TextualForecast($title, $text, $from, $to);
    }

    /**
     * @return String
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return String
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return \Datetime
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return \DateTime
     */
    public function getTo()
    {
        return $this->to;
    }
}
