<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Pages API
 *
 */
class Pages extends MY_Controller {

  function __construct()
  {
    parent::__construct();
    $this->data = new stdClass();
  }
  
  function index()
  {
  
  }
  
  function coming_soon()
  {
    $this->load->view('coming_soon');
  }
  
  // ------------------------------------------------------------------------

  function template_top()
  {
    //session_start();
      
    //$this->load->helper(array('url', 'html', 'form', 'string', 'frontend'));
    
    $_logged_in = array_key_exists('customer_sess', $_SESSION) ? $_SESSION["customer_sess"]["a"]->customers_id > 0 : FALSE;
    $this->data->logged_in = $_logged_in;
    $this->data->show_columns = FALSE;
    $this->data->site_mode = 'cc_mode';
    
    if($_logged_in)
    {
      $this->data->top_links = array(
        'customer_center/my_websites' => 'My Websites', 
        'customer_center/account_settings' => array(
          'Account Settings', 
          array(
            'customer_center/account_settings/general_information' => 'General Information',
            'customer_center/account_settings/billing_information' => 'Billing Information',
            'customer_center/account_settings/invoices' => 'Invoices',
            'customer_center/account_settings/update_login_details' => 'Update Login Details'
          ) 
        ),
        'customer_center/domain_names' => 'Domain Names', 
        'customer_center/support' => array(
          'Support',
          array(
            'customer_center/support/view_tickets' => 'View Tickets',
            'customer_center/support/submit_a_ticket' => 'Submit a Ticket',
            'customer_center/support/faq' => 'FAQ'
          )
          
      ));
    }
    else
    {
      $this->data->top_links = array(
        '' => 'Home',
        'how-it-works' => 'How It Works',
        'features' => 'Features',
        'plans' => 'Plans & Prices',
        'showcase' => 'Customer Showcase',
        'design' => 'Design Your Site'
      );
    } 

    $this->data->hide_nav = FALSE;
    $this->data->curr_page = '';
    $this->load->view('inc/header', $this->data);
  }
  
  // ------------------------------------------------------------------------
  
  function design_tools()
  {
    $this->load->model(array('front_m', 'industry_m'));
    //$this->load->helper(array('url', 'html', 'form', 'string', 'frontend'));
    $this->data->thumbs = $this->front_m->get_design_thumbs();
    $this->data->form_action = '/api/drupal/change_theme';
    
    $this->data->industries = $this->industry_m->get_industries();
    
    $cookie = unserialize($_COOKIE['ZeitSite']);
    $site = ZW_Cache::lookup($cookie['site']);
    $dbpre = $this->db->dbprefix;
    $this->db->dbprefix = '';
    $query = $this->db->select('industry_id')->where('websites_id', $site->websites_id)->limit(1)->get('websites');
    $this->db->dbprefix = $dbpre;
    if($query->num_rows() > 0 && $query->row()->industry_id > 0)
    {
      $this->data->choose_industry = FALSE;
      $this->data->default_industry = $query->row()->industry_id;
    }
    else
    {
      $this->data->choose_industry = TRUE;
      $this->data->default_industry = 1;
    }
    
    
    $this->load->view('frontend/design', $this->data);
  }

  // ------------------------------------------------------------------------

  function color_schemes($theme_name)
  {
    $this->load->model(array('image_m', 'industry_m', 'drupal_m', 'customer_center/websites_model'));
    //$this->load->model(array('image_m', 'industry_m', 'customer_center/websites_model'));
    if (isset($_SESSION["customer_sess"]["a"])) {
      list($websites_a,$w_setup) = $this->websites_model->get_websites();
      $website_id = $websites_a[0]['websites_id'];
      $ci_theme_settings = unserialize($websites_a[0]['theme_settings']);
      $this->data->industry_image = (isset($ci_theme_settings['industry_image'])) ? $ci_theme_settings['industry_image']: "$theme_name/blah";
    }
    else {
      $website_id = NULL;
      $this->data->industry_image = "$theme_name/blah";
    }
    $this->drupal_m->set_db(NULL);
    $theme_settings = $this->drupal_m->get_variable('theme_'.$theme_name.'_settings');
/*
if (!is_null($theme_settings)) {
  echo "ZW Theme $theme_name:<pre>".print_r($theme_settings,true)."</pre>\n";
  }
*/
    //$this->drupal_m->set_db($website_id);
    
    //$theme_settings = array();
    //if (isset($ci_theme_settings)) $theme_settings = $ci_theme_settings;
  
    //if (
    if ($website_id != NULL) {
      $this->data->theme_palette = $this->drupal_m->get_variable('color_'.$theme_name.'_palette');
      //$color_scheme = $theme_settings['color_scheme'];
      //echo "Theme settings: <pre>".print_r($theme_settings,true)."</pre><br>\n";
      //if (isset($theme_settings['color_scheme_values_'.$color_scheme])) {
      if (isset($theme_settings['scheme'])) {
        //$this->data->theme_palette = $theme_settings['color_scheme_values_'.$color_scheme];
        $this->data->theme_palette = $theme_settings['scheme'];
        }
      }
    else {
      //$this->data->theme_palette = array();
      }
    /*
    if( ! empty($this->data->theme_palette))
    {
      $scheme_key = implode(',',$this->data->theme_palette);
      if (array_key_exists('info',$theme_settings))
      {
        if(array_key_exists($scheme_key, $theme_settings['info']['schemes']))
        {
          $this->data->current_scheme = url_title($theme_settings['info']['schemes'][$scheme_key], 'underscore', TRUE);
        }
        else
        {
          $this->data->current_scheme = 'custom';
        }
      }
      else
      {
        $this->data->current_scheme = 'custom';
      }
    }
    */
    
    /*if (isset($theme_settings['color_scheme']))
    {
      $this->data->current_scheme = $theme_settings['color_scheme'];
    }
    else
    {
      $this->data->current_scheme = NULL;
    }*/
    if (isset($ci_theme_settings['color_scheme']))
    {
      $this->data->current_scheme = $ci_theme_settings['color_scheme'];
    }
    else
    {
      $this->data->current_scheme = NULL;
    }
    //echo "Theme: $theme_name<br>\n";
    $query = $this->db->query("SELECT zw_name,custom_colors FROM zw_themes WHERE name = '$theme_name' LIMIT 1");
    $this->data->nicename = $query->num_rows() > 0 ? $query->row()->zw_name : '';
    $this->data->customcols = $query->num_rows() > 0 ? $query->row()->custom_colors : ''; 


    $this->data->theme_name = $theme_name;
    $this->data->schemes = 0;
    $this->data->color_schemes = array();

    $this->data->industries = $this->industry_m->get_industries();
    //$cookie = unserialize($_COOKIE['ZeitSite']);
    //$site = ZW_Cache::lookup($cookie['site']);
    
    /*if(isset($site->websites_id))
    {
      $dbpre = $this->db->dbprefix;
      $this->db->dbprefix = '';
      $query = $this->db->select('industry_id')->where('websites_id', $site->websites_id)->limit(1)->get('websites');
      $this->db->dbprefix = $dbpre;

      if($query->num_rows() > 0 && $query->row()->industry_id > 0)
      {
        $this->data->choose_industry = FALSE;
        $this->data->default_industry = $this->industry_m->get_industry($query->row()->industry_id);
      }
      else
      {
        $this->data->choose_industry = TRUE;
        $this->data->default_industry = $this->industry_m->get_industry(0);
      }
    }
    else
    {
      $this->data->choose_industry = TRUE;
      $this->data->default_industry = $this->industry_m->get_industry(0);
    }*/
    $this->data->is_master = ($this->session->userdata('websites_id') == '') ? true : false;
    $this->data->choose_industry = TRUE;
    $this->data->default_industry = FALSE;

    //$def_id = ( ! $this->data->default_industry === FALSE) ? $this->data->default_industry->websites_industries_id : -1;
    if ($website_id != NULL) {
      $def_id = $websites_a[0]['industry_id'];
      $this->data->default_industry = $websites_a[0]['industry_id'];
      }
    else {
      $def_id = -1;
      }
    $this->data->industry_image_dropdown = $this->load->view('ajax/partial/industry_image_dropdown', 
      array(
        'industry_image' => $this->data->industry_image,
        'industry_images' => $this->image_m->get_theme_images($def_id),
        'theme_name' => $theme_name,
        //'default_ind' => str_replace('ind_', '', $this->drupal_m->get_content('theme_header', 'field_theme_industry_value', '')),
        'default_ind' => $def_id,
        'is_master' => $this->data->is_master
      ),
      TRUE
    );
  if (!is_null($theme_settings)) {
    if(array_key_exists('info', $theme_settings))
    //if (array_key_exists('color_scheme',$theme_settings))
    {
      $cnt = 0;
      //foreach($theme_settings['info']['schemes'] as $colors => $name)
      //echo "Theme settings: <br><pre>\n".print_r($theme_settings, true)."</pre><br>\n";
      foreach ($theme_settings['info']['schemes'] as $colors => $scheme)
      {
        /*
        if (strpos($scheme,'color_scheme_values_') === 0) {
          $name = substr($scheme,20);
          }
        else continue;
        */
        $name = $scheme;
        $color_arr = explode(',', $colors);
        if(strlen($colors) > 24)
        {
          //echo "In color schemes loop, $name<br>\n";
          $this->data->color_schemes[$name] = $color_arr;
          $this->data->schemes++;

          /*
          if($cnt === 0 AND empty($this->data->current_scheme))
          {
            $this->data->current_scheme = $name;
          }
          */
          //if ($theme_settings['scheme'] == $colors) {
          //echo "Name: $name, Color Scheme: $ci_theme_settings[color_scheme]<br>\n";
          if (isset($ci_theme_settings)) {
            if (isset($ci_theme_settings['color_scheme'])) {
              if ($ci_theme_settings['color_scheme'] == $name) {
                $this->data->current_scheme = $name;
                }
              }
            }
        }
        $cnt++;
      }
      //echo "Current Theme: ".$this->data->current_theme.", CI: $ci_theme_settings[theme_name], ZW: $theme_settings[theme]<br>\n";
      if (isset($ci_theme_settings)) {
        $this->data->ci_theme_settings = $ci_theme_settings;
        if (isset($ci_theme_settings['color_scheme'])) {
          if (!isset($this->data->current_scheme) && 
            ($ci_theme_settings['color_scheme'] == 'custom') &&
            ($ci_theme_settings['theme_name'] == $theme_settings['theme'])) {
            $this->data->current_scheme = 'custom';
            }
          }
        }
      if($this->data->current_scheme == 'custom')
      {
        //$this->data->color_schemes['custom'] = array_values($this->data->theme_palette);
        $this->data->color_schemes['custom'] = explode(',',$this->data->theme_palette);
      }
    }
  }
  $this->load->view('ajax/color_schemes', $this->data);
}
  
  
  function industry_image_dropdown($theme_name, $industry_id=1)
  {
    $this->load->model(array('image_m', 'industry_m'));
    $this->load->view('ajax/partial/industry_image_dropdown', 
      array(
        'industry_images' => $this->image_m->get_theme_images($industry_id),
        'theme_name' => $theme_name
      )
    );
  }
  
  // ------------------------------------------------------------------------
  
  function image_library()
  { 
    $_args = func_get_args();
    
    if((count($_args) < 2) OR ! in_array($_args[0], array('industry', 'tag', 'user')))
      exit('Invalid arguments');    
    
    $_type = $_args[0];
    $_which = $_args[1];
    $_refresh = (array_key_exists(2, $_args) && $_args[2] > 0) ? TRUE : FALSE;
    
    $this->load->model('image_m');
    $this->load->helper(array('form'));
    switch($_type)
    {
      case 'industry' :
      case 'tag' :
        
        $_data = array(
          'title'     => 'Image Library',
          'path'      => site_url('images/zw_library').'/',
          'industries'  => $this->image_m->get_type('industry'),
          'tags'      => $this->image_m->get_type('tag'),
          'current'   => $_which
        );

      break;
      case 'user' :
        
        $_data = array(
          'title'     => 'Your Uploaded Images',
          //'path' => 'http://' . $_which . '/sites/sergey022.readysetlaunch.com/files/'
          //'path' => 'http://' . $_which . '/sites/' . $_which . '/files/'
          'path' => 'http://' . $_which . '/sites/' . $_which . '/files/'
          //'path' => site_url('sites/zeitsites/'.$_which.'/my_images').'/'
        );
        
      break;
    }

    $_data['type'] = $_type;
    $_data['images'] = $this->image_m->get_images($_type, $_which);
    
    $this->load->vars($_data);
    if($_refresh === TRUE){
      if($_type == 'user'){
        $_view = 'ajax/image_library/uploaded_images';
      }
      else{
        $_view = 'ajax/image_library/images';
      }
    }
    else{
      if($_type == 'user'){
        $_view = 'ajax/image_library/uploaded_page';
      }
      else{
        $_view = 'ajax/image_library/page';
      }
    }

    $this->load->view($_view);
  }
  
  // ------------------------------------------------------------------------

} // end class Pages
