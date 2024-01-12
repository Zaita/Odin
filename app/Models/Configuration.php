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
      "title" => "SDLT",
      "footerText" => "Copyright (c) 2023 Scott Rasmussen (github/zaita)",
      "sdltEmail" => "sdlt@example.com",
      "securityTeamEmail" => "security@example.com",
      "themeBgColor" => "#E5E5E5",
      "themeTextColor" => "#000000",
      "themeHeaderColor" => "#800000",
      "themeHeaderTextColor" => "#FFFFFF",
      "themeSubheaderColor" => "#AB0000",
      "themeSubheaderTextColor" => "#FFFFFF",
      "themeBreadcrumbColor" => "#ADADAD",
      "themeHyperlinkColor" => "#FF9933",
      "logoPath" => "/images/sdlt_base_logo.png",
      "subHeaderImagePath" => "/images/subheader.jpg"
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
      $json->footerText = $fields["footerText"];
      $json->alternateEmail = array_key_exists('alternateEmail', $fields) ? $fields["alternateEmail"]: '';
      $json->securityTeamEmail = $fields["securityTeamEmail"];

      $siteConfig->value = json_encode($json);
      $siteConfig->save();
    }

    /**
     * 
     */
    protected static $defaultDashboardValues = array(
      "title" => "Welcome to the SDLT",
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
