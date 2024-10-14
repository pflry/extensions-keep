<?php

class EXTENSIONS_KEEP_Icons {

  protected static $icons = array(   
    'file-arrow-down' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" class="icon file-arrow-down"><rect width="256" height="256" fill="none"/><path d="M200,224H56a8,8,0,0,1-8-8V40a8,8,0,0,1,8-8h96l56,56V216A8,8,0,0,1,200,224Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><polyline points="152 32 152 88 208 88" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="128" y1="120" x2="128" y2="184" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><polyline points="104 160 128 184 152 160" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>',
    
    'file-arrow-up' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" class="icon file-arrow-up"><rect width="256" height="256" fill="none"/><path d="M200,224H56a8,8,0,0,1-8-8V40a8,8,0,0,1,8-8h96l56,56V216A8,8,0,0,1,200,224Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><polyline points="152 32 152 88 208 88" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><polyline points="104 144 128 120 152 144" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="128" y1="184" x2="128" y2="120" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>',
    
    'notepad' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" class="icon notepad"><rect width="256" height="256" fill="none"/><line x1="96" y1="128" x2="160" y2="128" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="96" y1="160" x2="160" y2="160" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M48,40H208a0,0,0,0,1,0,0V200a24,24,0,0,1-24,24H72a24,24,0,0,1-24-24V40A0,0,0,0,1,48,40Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="80" y1="24" x2="80" y2="56" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="128" y1="24" x2="128" y2="56" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="176" y1="24" x2="176" y2="56" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>'
  );

  public static function get_svg($icon) {
    if (!is_string($icon)) {
      return '';
    }
    if (self::icon_exists($icon)) {
      return self::$icons[$icon];
    }
    return '';
  }

  public static function get_all_icons() {
    return self::$icons;
  }

  public static function icon_exists($icon) {
    return array_key_exists($icon, self::$icons);
  }
}