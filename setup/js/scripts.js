function SetSkinSelection( skin )
{
    var f = $("skinfield");
    f.value = skin;
    
    var imgs = new Array();
    imgs = GetByClass( "skinthumb", "", "img" );
    for ( i=0; i<imgs.length; i++ )
    {
        imgs[i].style.border = "2px solid #FA0";
    }

    var img = $( "img_" + skin );
    img.style.border = "2px solid #006";
}


function $() 
{
    var elements = new Array();
    for (var i = 0; i < arguments.length; i++) 
    {
        var element = arguments[i];
        if (typeof element == "string")
            element = document.getElementById(element);
        if (arguments.length == 1)
            return element;
        elements.push(element);
    }
    return elements;
}

function GetByClass( ClassName, NodeToSearch, TagToMatch )
{
    var Matches = new Array();
    if ( NodeToSearch == null ||
         NodeToSearch == "undefined" ||
         NodeToSearch == "" )
    {
        NodeToSearch = document;
    }
    if ( TagToMatch == null ||
         TagToMatch == "undefined" ||
         TagToMatch.replace(/^\s*|\s*$/g,"") == "" )
    {
        TatToMatch = "*";
    }
    var els = NodeToSearch.getElementsByTagName( TagToMatch );
    var len = els.length;
    var pattern = new RegExp("(^|\\s)"+ClassName+"(\\s|$)");
    for ( i=0; i<len; i++ ) 
    {
        if ( pattern.test(els[i].className) ) 
        {
            Matches.push(els[i]);
        }
    }
    return Matches;
}
