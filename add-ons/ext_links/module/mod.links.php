<?php

defined('SKYBLUE') or die(basename(__FILE__));

global $Core;

###########################################################################################
# If either of our data sources do not exist, just exit.
###########################################################################################

if (!file_exists(SB_XML_DIR.'links.xml') || 
    !file_exists(SB_XML_DIR.'linksgroups.xml'))
{
    exit(0);
}

###########################################################################################
# Load our data objects.
###########################################################################################

$items  = $Core->xmlHandler->parserMain(SB_XML_DIR.'links.xml');
$groups = $Core->xmlHandler->parserMain(SB_XML_DIR.'linksgroups.xml');

###########################################################################################
# If there are no groups or no links, just exit.
###########################################################################################

if (!count($groups) || !count($items)) exit(0);

###########################################################################################
# We have valid data sources and have found some objects
###########################################################################################

$html = null;
foreach ($groups as $group)
{
    $ListItems = null;
    foreach ($items as $item)
    {
        $attrs = array('href'=>$item->url);
        if (isset($item->relationship) && trim($item->relationship) != '')
        {
            $attrs['rel'] = $item->relationship;
        }
        if ($item->group == $group->id)
        {
            $ListItems .= $Core->HTML->MakeElement(
                'li', 
                array(), 
                $Core->HTML->MakeElement(
                    'a', 
                    $attrs, 
                    ucwords($item->name)
                )
            );
        }
    }
    $html .= $Core->HTML->MakeElement(
        'h2', 
        array('class'=>'linksgroup'),
         $group->name 
    );
    if (isset($group->blurb) && trim($group->blurb) != '')
    {
        $html .= $Core->HTML->MakeElement(
            'p',
            array(),
            str_replace('[br]', '<br />', $group->blurb)
        );
    }
    $html .= $Core->HTML->MakeElement(
        'ul', 
        array('class'=>'links'), 
        $ListItems
    );
}
echo $html;

?>
