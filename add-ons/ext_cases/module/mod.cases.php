<?php


defined('SKYBLUE') or die(basename(__FILE__));

global $Core;

$cases = $Core->xmlHandler->parserMain( SB_XML_DIR.'cases.xml' );
$who   = $Core->GetVar( $_GET, 'show', '' );
$case  = GetCase( $cases, $who );

echo ShowOthers( $cases, $case->id );
echo ShowCase( $case );


// FUNCTIONS

function ShowCase( $case )
{
    global $Core;
    
    if ( !empty( $case->photo ) )
    {
        $img  = $case->photo;
        $w    = $Core->imageWidth( $img );
        $h    = $Core->imageHeight( $img );
    } 
    $html  = '<div id="case">'."\r\n";
    $html .= '<div id="caseinset">'."\r\n";
    $html .= '<img src="'.$img.'"'."\r\n";
    $html .= str_repeat( ' ', 5 ).'width="'.$w.'"'."\r\n";
    $html .= str_repeat( ' ', 5 ).'height="'.$h.'"'."\r\n";
    $html .= str_repeat( ' ', 5 ).'alt="photo of '.$case->title.'"'."\r\n";
    $html .= str_repeat( ' ', 5 ).'title="'.$case->title.'" '."\r\n";
    $html .= str_repeat( ' ', 5 ).'/>'."\r\n";
    if ( !empty( $case->title ) ||
         !empty( $case->phone ) )
    {
        $html .= '<p>'."\r\n";
        $html .= !empty( $case->title ) ? '<span class="casename">'.$case->title.'</span>'."\r\n" : '';
        if ( !empty( $case->url ) )
        {
            $html .= ': <a href="'.$case->url.'">'.str_replace( 'http://', '', $case->url ).'</a>'."\r\n";
        }
        $html .= '</p>'."\r\n";
    }
    $html .= '</div>'."\r\n";
    $html .= '<table cellpadding="0" cellspacing="0" id="caseoverview">'."\r\n";
    if ( !empty( $case->projecttype ) )
    {
        $html .= '<tr>'."\r\n";
        $html .= '<td class="label">Project Type:</td><td>'.$case->projecttype.'</td>'."\r\n";
        $html .= '</tr>'."\r\n";
    }
    if ( !empty( $case->technologies ) )
    {
        $html .= '<tr>'."\r\n";
        $html .= '<td class="label">Technologies:</td><td>'.$case->technologies.'</td>'."\r\n";
        $html .= '</tr>'."\r\n";
    }
    if ( !empty( $case->client ) )
    {
        $html .= '<tr>'."\r\n";
        $html .= '<td class="label">Client:</td><td>'.$case->client.'</td>'."\r\n";
        $html .= '</tr>'."\r\n";
    }
    if ( !empty( $case->designer ) )
    {
        $html .= '<tr>'."\r\n";
        $html .= '<td class="label">Designer:</td><td>'.$case->designer.'</td>'."\r\n";
        $html .= '</tr>'."\r\n";
    }
    if ( !empty( $case->developer ) )
    {
        $html .= '<tr>'."\r\n";
        $html .= '<td class="label">Developer:</td><td>'.$case->developer.'</td>'."\r\n";
        $html .= '</tr>'."\r\n";
    }
    $html .= '</table>'."\r\n";
    if ( !empty( $case->story ) )
    {
        $html .= $Core->SBReadFile(SB_STORY_DIR.$case->story);
    }
    $html .= '</div>'."\r\n";
    
    return $html;
}
 
function ShowOthers( $cases, $who )
{
    global $Core;
    
    $pid = $Core->GetVar( $_GET, 'pid', DEFAULT_PAGE );
    
    $i=0;
    $nav = '<ul id="casesnav">'."\r\n";
    foreach( $cases as $p )
    {
        # if ( $p->id != $who )
        # {
            if ( !empty( $p->thumb ) )
            {
                $img  = $p->thumb;
                $w    = $Core->imageWidth( $img );
                $h    = $Core->imageHeight( $img );
            } 
            else 
            {
                $img = 'images/clear.gif';
                $w   = 25;
                $h   = 30;
            }
            if ( $i == 0 )
            {
                $id = ' id="firstthumb"';
            } 
            else if ( $i == count( $cases ) - 1 ) 
            {
                $id = ' id="lastthumb"';
            } 
            else 
            {
                $id = '';
            }
            $class = $p->id == $who ? ' class="active"' : '' ;

            $nav .= str_repeat( ' ', 4 ).'<li'.$id.$class.'>'."\r\n";
            if ( $p->title != 'null' )
            {
                if ( USE_SEF_URLS )
                {
                    $search = array( ' ', '.', ',', '&' );
                    $replace = array( '-', NULL, NULL, 'and' );
                    $name = str_replace( $search, $replace, $p->title);
                    $nav .= str_repeat( ' ', 8 ).'<a href="'.BASE_URI.$name.'-pg-'.$pid.'-'.$p->id.'.htm">'."\r\n";
                } 
                else 
                {
                    $nav .= str_repeat( ' ', 8 ).'<a href="'.BASE_URI.'index.php?pid='.$pid.'&show='.$p->id.'">'."\r\n";
                }
            } 
            else 
            {
                $nav .= str_repeat( ' ', 8 ).'<span class="noimg">'."\r\n";
            }
            $nav .= str_repeat( ' ', 12 ).'<img src="'.$img.'" '."\r\n";
            $nav .= str_repeat( ' ', 17 ).'width="'.$w.'" '."\r\n";
            $nav .= str_repeat( ' ', 17 ).'height="'.$h.'" '."\r\n";
            $nav .= str_repeat( ' ', 17 ).'alt="thumbail for '.$p->title.'" '."\r\n";
            $nav .= str_repeat( ' ', 17 ).'title="'.$p->title.'" '."\r\n";
            $nav .= str_repeat( ' ', 17 ).'/>'."\r\n";
            if ( $p->title != 'null' )
            {
                $nav .= str_repeat( ' ', 8 ).'</a>'."\r\n";
            } else {
                $nav .= str_repeat( ' ', 8 ).'</span>'."\r\n";
            }
            $nav .= str_repeat( ' ', 4 ).'</li>'."\r\n"; 
            $i++;
        # }
    }
    $nav .= '</ul>'."\r\n";
    return $nav;
}

function GetCase( $cases, $who )
{
    global $Core;
    if ( !empty( $who ) )
    {
        $case = $Core->selectObj( $cases, $who );
    } 
    else 
    {
        $case = $cases[0];
    }
    return $case;
}

?>