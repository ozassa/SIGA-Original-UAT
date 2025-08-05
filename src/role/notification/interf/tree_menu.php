<?php class HTML_TreeMenu
{
  /**
    * Indexed array of subnodes
  * @var array
    */
  var $items;

  /**
    * The layer ID
  * @var string
    */
  var $layer;

  /**
    * Path to the images
  * @var string
    */
  var $images;

  /**
    * Name of the object
  * This should not be changed without changing
  * the javascript.  (no longer true)
  * @var string
    */
  var $menuobj;

  /**
    * Constructor
  *
  * @access public
  * @param  string $layer          The name of the layer to add the HTML to.
  *                                In browsers that do not support document.all
  *                                or document.getElementById(), document.write()
  *                                is used, and thus this layer name has no effect.
  * @param  string $images         The path to the images folder.
  * @param  string $linkTarget     The target for the link. Defaults to "_self"
  * @param  string $usePersistence Whether to use clientside persistence. This option
  *                                only affects ie5+.
    */
  function HTML_TreeMenu($layer=null, $images=null, $linkTarget = '_self', $usePersistence = true)
  {
    //$this->menuobj        = 'objTreeMenu';  // No longer used here. cc 2002-10-31
   
   
    $this->layer          = $layer;
    $this->images         = $images;
    $this->linkTarget     = $linkTarget;
    $this->usePersistence = $usePersistence;
  }

  /**
    * This function adds an item to the the tree.
  *
  * @access public
  * @param  object $menu The node to add. This object should be
  *                      a HTML_TreeNode object.
  * @return object       Returns a reference to the new node inside
  *                      the tree.
  */
  function &addItem(&$menu)
  {
    $this->items[] = &$menu;
    return $this->items[count($this->items) - 1];
  }

  /**
    * This function prints the menu Jabbascript code. Should
  * be called *AFTER* your layer tag has been printed. In the
  * case of older browsers, eg Navigator 4, The menu HTML will
  * appear where this function is called.
  *
  * @access public
  * @param  string $layer          The name of the layer to add the HTML to.
  *                                In browsers that do not support document.all
  *                                or document.getElementById(), document.write()
  *                                is used, and thus this layer name has no effect.
  * @param  string $images         The path to the images folder.
  */
  function printMenu($images=null, $layer=null)
  {
    
    // Setting menuobj here, rather than in constructor, ensures that
    // multiple copies of the menu have different JavaScript variable names.
    static $_menuobjcnt=0;
    $this->menuobj        = 'objTM'.$_menuobjcnt++;
    // $images and $layer could have been set in the constructor,
    // so don't assign to them unless a value has been supplied here.
    if (!empty($images))     $this->images = $images;
    if (!empty($layer))      $this->layer  = $layer;

    echo "\n";
    echo '<script language="javascript" type="text/javascript">' . "\n\t";
    echo sprintf('%s = new TreeMenu("%s", "%s", "%s", "%s");',
                 $this->menuobj,
                 empty($this->layer) ? $this->menuobj : $this->layer,
                 $this->images,
                 $this->menuobj,
                 $this->linkTarget);

    echo "\n";
    if (isset($this->items)) {
      for ($i=0; $i<count($this->items); $i++) {
        $this->items[$i]->_printMenu($this->menuobj . ".n[$i]");
        
      }
    }

    echo sprintf("\n\t%s.drawMenu();", $this->menuobj);
    
    if ($this->usePersistence) {
      echo sprintf("\n\t%s.resetBranches();", $this->menuobj);
    }
    echo "\n</script>\n"; // cc 2002-10-29 add trailing \n
    
  }
  

} // HTML_TreeMenu

/**
* HTML_TreeNode class
*
* This class is supplementary to the above and provides a way to
* add nodes to the tree. A node can have other nodes added to it.
*
* @author  Richard Heyes <richard@php.net>
* @author  Harald Radi <harald.radi@nme.at>
* @access  public
* @package HTML_TreeMenu
*/
class HTML_TreeNode
{
  /**
    * The text for this node.
  * @var string
    */
  var $text;

  /**
    * The link for this node.
  * @var string
    */
  var $link;

  /**
    * The icon for this node.
  * @var string
    */
  var $icon;

  /**
    * Indexed array of subnodes
  * @var array
    */
  var $items;

  /**
    * Whether this node is expanded or not
  * @var bool
    */
  var $expanded;

  /**
    * Constructor
  *
  * @access public
  * @param  string $text      The description text for this node
  * @param  string $link      The link for the text
  * @param  string $icon      Optional icon to appear to the left of the text
  * @param  bool   $expanded  Whether this node is expanded or not (IE only)
  * @param  bool   $isDynamic Whether this node is dynamic or not (no affect on non-supportive browsers)
    */
  function HTML_TreeNode($text = null, $link = null, $icon = null, $expanded = false, $isDynamic = true)
  {
    $this->text      = (string)$text;
    $this->link      = (string)$link;
    $this->icon      = (string)$icon;
    $this->expanded  = $expanded;
    $this->isDynamic = $isDynamic;
  }

  /**
    * Adds a new subnode to this node.
  *
  * @access public
  * @param  object $node The new node
    */
  function &addItem(&$node)
  {
    $this->items[] = &$node;
    return $this->items[count($this->items) - 1];
  }

  /**
    * Prints jabbascript for this particular node.
  *
  * @access private
  * @param  string $prefix The jabbascript object to assign this node to.
    */
  function _printMenu($prefix)
  {
    echo sprintf("\t%s = new TreeNode('%s', %s, %s, %s, %s);\n",
                 $prefix,
                 addslashes($this->text), // cc 2002-11-01.  Text shouldn't
		                          // be slashed by user since static menus whill show the slashes.
                 !empty($this->icon) ? "'" . $this->icon . "'" : 'null',
                 !empty($this->link) ? "'" . $this->link . "'" : 'null',
                 $this->expanded  ? 'true' : 'false',
                 $this->isDynamic ? 'true' : 'false');

    if (!empty($this->items)) {
      for ($i=0; $i<count($this->items); $i++) {
        $this->items[$i]->_printMenu($prefix . ".n[$i]");
        
      }
    }
  }
}



// default style classes for use in tree text.
static $_autostyles = array('tmenu0text', 'tmenu1text', 'tmenu2text', 'tmenu3text');

class HTML_TreeNodeStyle extends HTML_TreeNode
{
  var $style = false;

  // Constructor
  function HTML_TreeNodeStyle($text = null, $link = null, $icon = null, $expanded = false, $isDynamic = true, $style = false)
  {
    HTML_TreeNode::HTML_TreeNode($text, $link, $icon, $expanded, $isDynamic);
    if ($style) $this->style = $style;
    else {
      // We want to use default style based on level in the tree
      // Unfortunately, we cannot know our level yet so defer...
      $this->style = 'auto';
    }
  } // HTML_TreeNodeStyle

  function style( $st )
  {
    $this->style = $st;
  } // style
 
  function _printMenu($prefix)
  {
    HTML_TreeNode::_printMenu($prefix);
    if ($this->style) echo "\t$prefix.style = '" . $this->style . "';\n";
    else echo "\t$prefix.style = false;\n";
  }	 // _printMenu
}

?>
