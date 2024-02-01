<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Configuration extends Model
{
    use HasFactory;

    protected $table = "configurations";

    protected $fillable = [
      'key',
      'value'      
    ];

    protected static $defaultSiteConfigValues = array(
      "title" => "Odin",
      "footer_text" => "Copyright (c) 2023 Scott Rasmussen (github/zaita)",
      "odin_email" => "odin@example.com",
      "security_team_email" => "security@example.com",
      "theme_login_bg_color" => "#333333",
      "theme_bg_color" => "#E5E5E5",
      "theme_text_color" => "#000000",
      "theme_header_color" => "#800000",
      "theme_header_text_color" => "#FFFFFF",
      "theme_subheader_color" => "#AB0000",
      "theme_subheader_text_color" => "#FFFFFF",
      "theme_breadcrumb_color" => "#ADADAD",
      "theme_hyperlink_color" => "#FF9933",
      "theme_btn_bg_color" => "#FFFFFF",
      "theme_btn_text_color" => "#800000",
      "theme_btn_hover_bg_color" => "#800000",
      "theme_btn_hover_text_color" => "#FFFFFF",
      "theme_input_bg_color" => "#FFFFFF",
      "theme_input_text_color" => "#000000",
      "theme_input_border_color" => "#800000",
      "theme_input_focus_border_color" => "#FF9933",
      "theme_error_text_color" => "#FF0000",    
      "logo_path" => "/images/odin_base_logo.png",
      "subheader_image_path" => "/images/subheader.jpg"      
    );

    // protected static $defaultAdminSiteConfigValues = array(
    //   "admin_logo_path" => "/images/odin_base_logo.png",
    //   "theme_admin_bg_color" => "#E3EAFA",
    //   "theme_admin_help_bg_color" => "#E3EAFA",
    //   "theme_admin_help_text_color" => "#658CE9",
    //   "theme_admin_menu_bg_color" => "#658CE9",
    //   "theme_admin_menu_logout_border_color" => "#400000",
    //   "theme_admin_menu_parent_text_color" => "#E3EAFA",
    //   "theme_admin_menu_text_color" => "#E3EAFA",
    //   "theme_admin_menu_selected_bg_color" => "#E3EAFA",
    //   "theme_admin_menu_selected_text_color" => "#658CE9",
    //   "theme_admin_topmenu_bg_color" => "#658CE9",
    //   "theme_admin_topmenu_breadcrumb_color" => "#E3EAFA",
    //   "theme_admin_topmenu_border_color" => "#658CE9",
    //   "theme_admin_topmenu_item_border_color" => "#FFFFFF",
    //   "theme_admin_topmenu_item_text_color" => "#E3EAFA",
    //   "theme_admin_content_bg_color" => "#FFFFFF",
    //   "theme_admin_content_text_color" => "#000000",      
    //   "theme_admin_content_spacer" => "#658CE9",
    // );

    protected static $defaultAdminSiteConfigValues = array(
      "admin_logo_path" => "/images/odin_base_logo.png",
      "theme_admin_bg_color" => "#092635",
      "theme_admin_help_bg_color" => "#E4DEBE",
      "theme_admin_help_text_color" => "#E4DEBE",
      "theme_admin_menu_bg_color" => "#092635",
      "theme_admin_menu_logout_border_color" => "#9EC8B9",
      "theme_admin_menu_parent_text_color" => "#9EC8B9",
      "theme_admin_menu_text_color" => "#9EC8B9",
      "theme_admin_menu_selected_bg_color" => "#9EC8B9",
      "theme_admin_menu_selected_text_color" => "#092635",
      "theme_admin_topmenu_bg_color" => "#092635",
      "theme_admin_topmenu_breadcrumb_color" => "#9EC8B9",
      "theme_admin_topmenu_border_color" => "#9EC8B9",
      "theme_admin_topmenu_item_border_color" => "#9EC8B9",
      "theme_admin_topmenu_item_text_color" => "#9EC8B9",
      "theme_admin_content_bg_color" => "#9EC8B9",
      "theme_admin_content_text_color" => "#000000",      
      "theme_admin_content_spacer" => "#1B4242",
    );

    public static function site_config() {
      $base = Configuration::$defaultSiteConfigValues;
      $admin = Configuration::$defaultAdminSiteConfigValues;

      $result = array_merge($base, $admin);
      $result = json_decode(json_encode($result));
    
      // Light Mode
      $color1 = "#FFFFFF";
      $color2 = "#777777";
      $color3 = "#555555";
      $color4 = "#FFFFFF";

      // Dark Mode
      $color1 = "#000000";
      $color2 = "#999999";
      $color3 = "#333333";
      $color4 = "#FFFFFF";

      // Colour Mode
      // $color1 = "#12372A";
      // $color2 = "#ADBC9F";
      // $color3 = "#436850";
      // $color4 = "#FBFADA";

      $result->theme_admin_bg_color = $color2;
      $result->theme_admin_help_bg_color = $color1;
      $result->theme_admin_help_text_color = $color2;
      $result->theme_admin_menu_bg_color = $color1;
      $result->theme_admin_menu_logout_border_color = $color2;
      $result->theme_admin_menu_parent_text_color = $color2;
      $result->theme_admin_menu_text_color = $color2;
      $result->theme_admin_menu_selected_bg_color = $color2;
      $result->theme_admin_menu_selected_text_color = $color1;
      $result->theme_admin_topmenu_bg_color = $color1;
      $result->theme_admin_topmenu_breadcrumb_color = $color2;
      $result->theme_admin_topmenu_border_color = $color2;
      $result->theme_admin_topmenu_item_border_color = $color2;
      $result->theme_admin_topmenu_item_text_color = $color2;
      $result->theme_admin_content_bg_color = $color3;
      $result->theme_admin_content_text_color = $color4;
      $result->theme_admin_content_spacer = $color4;

      $result->theme_btn_bg_color = $color3;
      $result->theme_btn_text_color = $color4;
      $result->theme_btn_hover_bg_color = $color4;
      $result->theme_btn_hover_text_color = $color3;
      $result->theme_input_bg_color = $color3;
      $result->theme_input_text_color = $color4;
      $result->theme_input_border_color = $color4;
      // $result->theme_input_focus_border_color = "#00FFFF";
      $result->theme_error_text_color = "#FF8888";


      return $result;
    }

    public static function GetSiteConfig() {
      $config = null; //Configuration::where('label', 'site_configuration')->first();
      if (is_null($config)) {
        $config = new Configuration();
        $config->label = "site_configuration";
        $config->value = json_encode(Configuration::$defaultSiteConfigValues);          
        $config->save();
      }

      $x = json_decode($config->value, true);
      $config->value = json_encode(array_merge($x, Configuration::$defaultAdminSiteConfigValues));
      return $config;
      // return $config;
    }

    public static function UpdateSiteConfig(array $fields) {
      $siteConfig = Configuration::GetSiteConfig();
      $json = json_decode($siteConfig->value);

      $json->title = $fields["title"];
      $json->footer_text = $fields["footer_text"];
      $json->alternateEmail = array_key_exists('alternateEmail', $fields) ? $fields["alternateEmail"]: '';
      $json->security_team_email = $fields["security_team_email"];

      $siteConfig->value = json_encode($json);
      $siteConfig->save();
    }

    
    public static function UpdateTheme(array $fields) {
      $siteConfig = Configuration::GetSiteConfig();
      $json = json_decode($siteConfig->value);

      $json->theme_login_bg_color = $fields["login_bg_color"];
      $json->theme_bg_color = $fields["bg_color"];
      $json->theme_text_color = $fields["text_color"];
      $json->theme_header_color = $fields["header_color"];
      $json->theme_header_text_color = $fields["header_text_color"];
      $json->theme_subheader_color = $fields["subheader_color"];
      $json->theme_subheader_text_color = $fields["subheader_text_color"];
      $json->theme_breadcrumb_color = $fields["breadcrumb_color"];
      $json->theme_hyperlink_color = $fields["hyperlink_color"];            

      $siteConfig->value = json_encode($json);
      $siteConfig->save();
    }

    /**
     * 
     */
    protected static $defaultDashboardValues = array(
      "title" => "Welcome to Odin",
      "titleText" => "Some silly title text",
      "submission" => "Create a new submission",
    );

    public static function GetDashboardConfig() {
      $config = Configuration::where('label', 'dashboard')->first();
      if (is_null($config)) {
        $config = new Configuration();
        $config->label = "dashboard";
        $config->value = json_encode(Configuration::$defaultDashboardValues);          
        $config->save();
      }

      return $config;
    }

    public static function UpdateDashboardConfig(array $fields) {
      $config = Configuration::GetDashboardConfig();
      $json = json_decode($config->value);

      $json->title = $fields["title"];
      $json->titleText = str_replace('class="readonly" contenteditable="true"', "", $fields["titleText"]);
      $json->submission = $fields["submission"];

      $config->value = json_encode($json);
      $config->save();
    }    

}
