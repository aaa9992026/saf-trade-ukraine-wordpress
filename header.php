<!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#0b3327"><?php wp_head(); ?></head><body <?php body_class(); ?>><?php wp_body_open(); ?>
<a class="saf-skip" href="#content">Перейти до вмісту</a>
<div class="saf-topbar"><div class="saf-container"><span>Оптові квіти для професійних покупців</span><span class="saf-topbar-right">ТОВ «САФ ТРЕЙД УКРАЇНА» · Україна</span></div></div>
<header class="saf-header" id="site-header"><div class="saf-container saf-nav">
<a class="saf-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="SAF Trade Ukraine — головна">
<?php if(has_custom_logo()): the_custom_logo(); else: ?><span class="saf-logo-icon"><svg viewBox="0 0 48 48" aria-hidden="true"><path d="M24 5c7 8 10 14 9 21-1 8-5 13-9 17-4-4-8-9-9-17C14 19 17 13 24 5Z" fill="currentColor"/><path d="M9 18c9 1 15 4 19 10-7 2-14 1-19-3-3-2-5-5-6-8 2 0 4 0 6 1Z" fill="currentColor" opacity=".58"/><path d="M39 18c-9 1-15 4-19 10 7 2 14 1 19-3 3-2 5-5 6-8-2 0-4 0-6 1Z" fill="currentColor" opacity=".72"/></svg></span><span class="saf-brand-copy"><strong>SAF TRADE</strong><small>UKRAINE</small></span><?php endif; ?>
</a>
<nav class="saf-menu" aria-label="Головна навігація"><?php wp_nav_menu(['theme_location'=>'primary','container'=>false,'menu_class'=>'saf-menu-list','fallback_cb'=>'saftrade_fallback_menu']); ?></nav>
<button class="saf-menu-toggle" aria-expanded="false" aria-label="Відкрити меню"><span></span><span></span><span></span></button>
<button class="saf-btn saf-btn-sm saf-nav-cta" type="button" data-saf-quote>Запросити ціни</button>
</div></header>
