<?php
/**
 * Form
 *
 * Construct and manages the form wrapping all fields
 */
namespace Former;

class Form
{
  /**
   * The Form type
   * @var string
   */
  public $type = null;

  /**
   * The available form types
   * @var array
   */
  private $availableTypes = array('horizontal', 'vertical', 'inline', 'search');

  /**
   * Opens up magically a form
   *
   * @param  string $typeAsked  The form type asked
   * @param  array  $parameters Parameters passed
   * @return string             A form opening tag
   */
  public function open($typeAsked, $parameters)
  {
    $action     = array_get($parameters, 0);
    $method     = array_get($parameters, 1, 'POST');
    $attributes = array_get($parameters, 2, array());
    $secure     = array_get($parameters, 3, false);

    // If classic form
    if($typeAsked == 'open') $type = Config::get('default_form_type');
    else {
      // Look for HTTPS form
      if (str_contains($typeAsked, 'secure')) {
        $typeAsked = str_replace('secure', null, $typeAsked);
        $secure = true;
      }

      // Look for file form
      if (str_contains($typeAsked, 'for_files')) {
        $typeAsked = str_replace('for_files', null, $typeAsked);
        $attributes['enctype'] = 'multipart/form-data';
      }

      // Calculate form type
      $type = trim(str_replace('open', null, $typeAsked), '_');
      if(!in_array($type, $this->availableTypes)) $type = Config::get('default_form_type');
    }

    // Add the final form type
    $attributes = Helpers::addClass($attributes, 'form-'.$type);

    // Store it
    $this->type = $type;

    // Fetch errors if asked for
    if (Config::get('fetch_errors')) {
      Former::withErrors();
    }

    // Open the form
    return \Form::open($action, $method, $attributes, $secure);
  }

  /**
   * Closes a Form
   *
   * @return string A closing <form> tag
   */
  public function close()
  {
    return '</form>';
  }
}
