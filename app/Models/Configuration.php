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
      "logo_path" => "/images/odin_base_logo.png",
      "subheader_image_path" => "/images/subheader.jpg"
    );

    public static function GetSiteConfig() {
      $config = Configuration::where('label', 'site_configuration')->first();
      if (is_null($config)) {
        $config = new Configuration();
        $config->label = "site_configuration";
        $config->value = json_encode(Configuration::$defaultSiteConfigValues);          
        $config->save();
      }

      return $config;
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
