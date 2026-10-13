<?php
/* v4.13 — hard-disable Jetpack Carousel at the source.
 * The WordPress.com/Jetpack Carousel injects a hidden photo-comment form into public HTML
 * (jp-carousel-comments-wrapper) even when normal post/page comments are closed.
 * This filter runs before Jetpack_Carousel::init() and prevents the carousel module from
 * enqueueing its assets/footer markup, so German carousel comment strings cannot exist
 * in View Source. The SAF Trade site does not require Jetpack's image carousel. */
add_filter( 'jp_carousel_maybe_disable', '__return_true', 0 );

/* Keep the underlying carousel comment preference off as a second line of defence.
 * This is harmless if Jetpack/WordPress.com does not expose the option in a given runtime. */
function saftrade_disable_jetpack_carousel_comments_option() {
    if ( get_option( 'carousel_display_comments', null ) !== 0 && get_option( 'carousel_display_comments', null ) !== '0' ) {
        update_option( 'carousel_display_comments', 0, false );
    }
    if ( get_option( 'carousel_enable_it', null ) !== 0 && get_option( 'carousel_enable_it', null ) !== '0' ) {
        update_option( 'carousel_enable_it', 0, false );
    }
}
add_action( 'admin_init', 'saftrade_disable_jetpack_carousel_comments_option', 1 );

if (!defined('ABSPATH')) exit;

const SAFTRADE_VERSION = '4.14.0';

function saftrade_setup(){
  load_theme_textdomain('saftrade', get_template_directory().'/languages');
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('custom-logo', ['height'=>140,'width'=>420,'flex-height'=>true,'flex-width'=>true]);
  add_theme_support('html5', ['search-form','gallery','caption','style','script']);
  add_theme_support('align-wide');
  add_theme_support('responsive-embeds');
  add_theme_support('editor-styles');
  add_post_type_support('page','excerpt');
  add_editor_style('assets/css/editor.css');
  register_nav_menus(['primary'=>'Головне меню','footer'=>'Меню в підвалі']);
}
add_action('after_setup_theme','saftrade_setup');


/* Final-site cleanup: comments and WordPress.com related/comment modules are disabled across
 * company pages, the flower catalogue, flower products, and all flower-category archives. */
function saftrade_disable_comments_support(){
  foreach(['page','flower'] as $type){
    if(post_type_supports($type,'comments')) remove_post_type_support($type,'comments');
    if(post_type_supports($type,'trackbacks')) remove_post_type_support($type,'trackbacks');
  }
}
add_action('init','saftrade_disable_comments_support',100);

function saftrade_no_comments_request(){
  if(is_admin()) return false;
  return is_page() || is_singular('flower') || is_post_type_archive('flower') || is_tax('flower_category');
}

add_filter('body_class',function($classes){
  if(saftrade_no_comments_request()) $classes[]='saf-comments-disabled';
  return $classes;
},30);

add_filter('comments_open',function($open,$post_id){
  $type=$post_id ? get_post_type($post_id) : '';
  if(in_array($type,['page','flower'],true) || saftrade_no_comments_request()) return false;
  return $open;
},999,2);
add_filter('pings_open',function($open,$post_id){
  $type=$post_id ? get_post_type($post_id) : '';
  if(in_array($type,['page','flower'],true) || saftrade_no_comments_request()) return false;
  return $open;
},999,2);
add_filter('comments_array',function($comments,$post_id){
  $type=$post_id ? get_post_type($post_id) : '';
  return (in_array($type,['page','flower'],true) || saftrade_no_comments_request()) ? [] : $comments;
},999,2);
add_filter('get_comments_number',function($count,$post_id){
  $type=$post_id ? get_post_type($post_id) : '';
  return (in_array($type,['page','flower'],true) || saftrade_no_comments_request()) ? 0 : $count;
},999,2);

/* If WordPress/Jetpack asks for a comments template anyway, return the deliberately empty template. */
add_filter('comments_template',function($template){
  if(saftrade_no_comments_request()) return get_template_directory().'/comments-disabled.php';
  return $template;
},999);


/* v4.12: WordPress.com/Jetpack hard-stop for public comment UI on catalogue/category pages.
 * This is intentionally defensive because WordPress.com can inject a remote comment form
 * outside the active theme's comments.php template. */
add_filter('wpcom_comments_form_enabled', function($enabled){
  return saftrade_no_comments_request() ? false : $enabled;
}, 999);
add_filter('jetpack_comments_can_comment', function($can_comment){
  return saftrade_no_comments_request() ? false : $can_comment;
}, 999);
add_filter('jetpack_comments_allow_comments', function($allowed){
  return saftrade_no_comments_request() ? false : $allowed;
}, 999);

add_action('wp_enqueue_scripts', function(){
  if(!saftrade_no_comments_request()) return;
  foreach(['comment-reply','jetpack-comments','jetpack-comment','jetpack-remote-comments','wpcom-comments'] as $handle){
    wp_dequeue_script($handle); wp_deregister_script($handle);
    wp_dequeue_style($handle);  wp_deregister_style($handle);
  }
}, 9999);

/* Inline, request-specific protection so the final cacheable HTML itself hides any late
 * comment module even before the external theme stylesheet/script has loaded. */
add_action('wp_head', function(){
  if(!saftrade_no_comments_request()) return;
  echo '<style id="saftrade-no-comments-v412">#comments,#respond,.comments-area,.comment-respond,.comment-form,.comment-navigation,.commentlist,.comment-list,.comments-title,.wp-block-comments,.wp-block-post-comments,.wp-block-post-comments-form,.wp-block-comments-title,.wp-block-comment-template,.jetpack-comments,.jetpack-comments-wrapper,.jetpack-comment-form,[class*="jetpack-comment"],[id*="jetpack-comment"],iframe[src*="comment" i],iframe[id*="comment" i],iframe[name*="comment" i],iframe[title*="comment" i],iframe[title*="kommentar" i],form[action*="wp-comments-post.php"],[data-testid*="comment" i]{display:none!important;visibility:hidden!important;height:0!important;min-height:0!important;max-height:0!important;margin:0!important;padding:0!important;border:0!important;overflow:hidden!important;opacity:0!important;pointer-events:none!important}</style>';
}, 1);

/* v4.14: selector-only late cleanup.  No user-facing legacy strings are embedded in
 * public HTML; this simply removes any comment/carousel markup injected after render. */
add_action('wp_footer', function(){
  if(!saftrade_no_comments_request()) return;
  ?>
  <script id="saftrade-no-comments-v414-js">
  (function(){
    const selectors=[
      '#comments','#respond','.comments-area','.comment-respond','.comment-form','.comment-navigation',
      '.commentlist','.comment-list','.comments-title','.wp-block-comments','.wp-block-post-comments',
      '.wp-block-post-comments-form','.wp-block-comments-title','.wp-block-comment-template',
      '.jetpack-comments','.jetpack-comments-wrapper','.jetpack-comment-form','[class*="jetpack-comment"]','[id*="jetpack-comment"]',
      '.jp-carousel-comments-wrapper','#jp-carousel-comments','#jp-carousel-comment-form-container','#jp-carousel-comment-form',
      '[class*="jp-carousel-comment"]','[id*="jp-carousel-comment"]',
      'iframe[src*="comment" i]','iframe[id*="comment" i]','iframe[name*="comment" i]',
      'form[action*="wp-comments-post.php"]','[data-testid*="comment" i]'
    ];
    function purge(){
      try{document.querySelectorAll(selectors.join(',')).forEach(el=>el.remove());}catch(e){}
    }
    purge(); setTimeout(purge,150); setTimeout(purge,700); setTimeout(purge,1800);
    new MutationObserver(purge).observe(document.documentElement,{childList:true,subtree:true});
  })();
  </script>
  <?php
}, 9999);

add_filter('jetpack_relatedposts_filter_enabled_for_request','__return_false',999);
add_filter('jetpack_relatedposts_filter_options',function($options){ if(is_array($options)) $options['enabled']=false; return $options; },999);
function saftrade_frontend_gettext_cleanup($translated,$text,$domain){
  if(is_admin()) return $translated;
  /* Keep only generic theme-safe localization. Comment UI is disabled at source. */
  if($text==='Ähnliche Beiträge') return 'Схожі матеріали';
  return $translated;
}
add_filter('gettext','saftrade_frontend_gettext_cleanup',20,3);

function saftrade_assets(){
  wp_enqueue_style('saftrade-main', get_template_directory_uri().'/assets/css/main.css', [], SAFTRADE_VERSION);
  wp_enqueue_script('saftrade-main', get_template_directory_uri().'/assets/js/main.js', [], SAFTRADE_VERSION, true);
  wp_localize_script('saftrade-main','SAFTrade',[ 
    'ajaxUrl'=>admin_url('admin-ajax.php'),
    'nonce'=>wp_create_nonce('saftrade_request'),
    'success'=>'Дякуємо! Запит отримано. Ми зв’яжемося з вами після перевірки деталей.',
    'error'=>'Не вдалося надіслати форму. Перевірте поля або спробуйте ще раз.',
    'fallbackImage'=>get_template_directory_uri().'/assets/images/placeholder-hero.svg'
  ]);
}
add_action('wp_enqueue_scripts','saftrade_assets');

function saftrade_images(){
  return [
    'hero'=>'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/Flower_market.jpg/1280px-Flower_market.jpg',
    'market'=>get_template_directory_uri().'/assets/images/wholesale-market.jpg',
    'greenhouse'=>'https://upload.wikimedia.org/wikipedia/commons/2/22/Flowers_in_a_greenhouse.jpg',
    'roses'=>get_template_directory_uri().'/assets/images/catalog-rose-light.jpg',
    'tulips'=>get_template_directory_uri().'/assets/images/catalog-tulips.jpg',
    'mums'=>get_template_directory_uri().'/assets/images/catalog-mums.jpg',
    'sunflowers'=>get_template_directory_uri().'/assets/images/catalog-sunflower.jpg',
    'about'=>get_template_directory_uri().'/assets/images/about-company-premium.jpg',
    'delivery'=>get_template_directory_uri().'/assets/images/delivery-logistics-premium.jpg',
    'sourcing'=>get_template_directory_uri().'/assets/images/catalog-seasonal.jpg',
    'wholesale'=>get_template_directory_uri().'/assets/images/wholesale-market.jpg',
    'quality'=>get_template_directory_uri().'/assets/images/catalog-mums.jpg',
    'faq'=>get_template_directory_uri().'/assets/images/catalog-tulips.jpg'
  ];
}
function saftrade_image($key='hero'){
  $images=saftrade_images();
  return $images[$key] ?? $images['hero'];
}

function saftrade_placeholder_image($key='hero'){
  if($key==='market') $key='wholesale';
  $allowed=['hero','roses','tulips','mums','sunflowers','greenhouse','about','delivery','sourcing','wholesale','quality','faq'];
  if(!in_array($key,$allowed,true)) $key='hero';
  return get_template_directory_uri().'/assets/images/placeholder-'.$key.'.svg';
}
function saftrade_flower_placeholder_image($post_id=0){
  $key='wholesale';
  if($post_id){
    $terms=get_the_terms($post_id,'flower_category');
    if($terms && !is_wp_error($terms)){
      $slug=$terms[0]->slug;
      $map=['troiandy'=>'roses','tyulpany'=>'tulips','khrizantemy'=>'mums','sonyashnyky'=>'sunflowers','sezonni'=>'wholesale'];
      $key=$map[$slug] ?? 'wholesale';
    }
  }
  return saftrade_placeholder_image($key);
}
function saftrade_flower_fallback_image($post_id=0){
  $key='market';
  if($post_id){
    $terms=get_the_terms($post_id,'flower_category');
    if($terms && !is_wp_error($terms)){
      $slug=$terms[0]->slug;
      $map=['troiandy'=>'roses','tyulpany'=>'tulips','khrizantemy'=>'mums','sonyashnyky'=>'sunflowers','sezonni'=>'market'];
      $key=$map[$slug] ?? 'market';
    }
  }
  return saftrade_image($key);
}
function saftrade_flower_image_url($post_id=0,$size='large'){
  /* Seeded catalogue examples use distinct local theme artwork so the public catalogue
     never repeats the same demo photograph. Real products added later can use their own
     featured images normally. */
  if($post_id && get_post_meta($post_id,'_saftrade_seeded_flower',true)==='1'){
    $demo=basename((string)get_post_meta($post_id,'_saftrade_demo_image',true));
    if($demo){
      $path=get_template_directory().'/assets/images/'.$demo;
      if(is_file($path)) return get_template_directory_uri().'/assets/images/'.$demo;
    }
  }
  if($post_id && has_post_thumbnail($post_id)){
    $url=get_the_post_thumbnail_url($post_id,$size);
    if($url) return $url;
  }
  return saftrade_flower_fallback_image($post_id);
}

function saftrade_catalog_url(){
  $url=get_post_type_archive_link('flower');
  return $url ? $url : add_query_arg('post_type','flower',home_url('/'));
}
function saftrade_page_url($slug,$fallback='/'){
  $page=get_page_by_path($slug,OBJECT,'page');
  if($page && $page->post_status==='publish'){
    $url=get_permalink($page);
    if($url) return $url;
  }
  return home_url($fallback);
}
function saftrade_term_url($slug){
  $term=get_term_by('slug',$slug,'flower_category');
  if($term && !is_wp_error($term)){
    $url=get_term_link($term);
    if(!is_wp_error($url)) return $url;
  }
  return saftrade_catalog_url();
}

function saftrade_preload_hero(){
  if(is_front_page()) echo '<link rel="preload" as="image" href="'.esc_url(saftrade_image('hero')).'" fetchpriority="high">' . "\n";
}
add_action('wp_head','saftrade_preload_hero',1);

function saftrade_register_content(){
  register_post_type('flower',[
    'labels'=>['name'=>'Квіти','singular_name'=>'Квітка','add_new'=>'Додати квітку','add_new_item'=>'Додати нову квітку','edit_item'=>'Редагувати квітку'],
    'public'=>true,'menu_icon'=>'dashicons-palmtree','has_archive'=>true,'rewrite'=>['slug'=>'kvitty'],'show_in_rest'=>true,'menu_position'=>21,
    'supports'=>['title','editor','thumbnail','excerpt','custom-fields']
  ]);
  register_taxonomy('flower_category','flower',[
    'labels'=>['name'=>'Категорії квітів','singular_name'=>'Категорія квітів'],'public'=>true,'hierarchical'=>true,'show_in_rest'=>true,'rewrite'=>['slug'=>'kategoriya-kvitiv']
  ]);
  register_post_type('saf_inquiry',[
    'labels'=>['name'=>'Оптові запити','singular_name'=>'Оптовий запит'],'public'=>false,'show_ui'=>true,'show_in_menu'=>true,'menu_icon'=>'dashicons-email-alt2',
    'supports'=>['title'],'capability_type'=>'post','map_meta_cap'=>true
  ]);
}
add_action('init','saftrade_register_content');

function saftrade_meta_boxes(){
  add_meta_box('saftrade_flower_details','Оптові характеристики','saftrade_flower_meta_box','flower','normal','high');
  add_meta_box('saftrade_inquiry_details','Дані запиту','saftrade_inquiry_meta_box','saf_inquiry','normal','high');
}
add_action('add_meta_boxes','saftrade_meta_boxes');

function saftrade_flower_meta_box($post){
  wp_nonce_field('saftrade_save_flower','saftrade_flower_nonce');
  $fields=[
    'saf_origin'=>['Походження / джерело партії','Вкажіть лише підтверджені дані або залиште порожнім'],
    'saf_grade'=>['Сорт / клас','Наприклад: сорт або комерційний клас'],
    'saf_pack'=>['Пакування','Вкажіть підтверджений формат пакування'],
    'saf_availability'=>['Доступність','За запитом / сезонно — лише якщо підтверджено'],
    'saf_moq'=>['Мінімальне замовлення','За запитом'],
    'saf_colors'=>['Кольори','Вкажіть доступні або орієнтовні відтінки'],
    'saf_stem'=>['Довжина стебла','Вкажіть підтверджений діапазон']
  ];
  foreach($fields as $key=>$cfg){
    $value=get_post_meta($post->ID,$key,true);
    echo '<p><label><strong>'.esc_html($cfg[0]).'</strong></label><br><input style="width:100%;padding:9px 11px" name="'.esc_attr($key).'" value="'.esc_attr($value).'" placeholder="'.esc_attr($cfg[1]).'"></p>';
  }
}
function saftrade_save_flower_meta($post_id){
  if(!isset($_POST['saftrade_flower_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['saftrade_flower_nonce'])),'saftrade_save_flower')) return;
  if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if(!current_user_can('edit_post',$post_id)) return;
  foreach(['saf_origin','saf_grade','saf_pack','saf_availability','saf_moq','saf_colors','saf_stem'] as $key){
    if(isset($_POST[$key])) update_post_meta($post_id,$key,sanitize_text_field(wp_unslash($_POST[$key])));
  }
}
add_action('save_post_flower','saftrade_save_flower_meta');

function saftrade_inquiry_meta_box($post){
  $keys=['name'=>'Ім’я','company'=>'Компанія','email'=>'E-mail','phone'=>'Телефон','city'=>'Місто доставки','interest'=>'Що цікавить','message'=>'Повідомлення','source'=>'Джерело'];
  echo '<table style="width:100%;border-collapse:collapse">';
  foreach($keys as $key=>$label){
    $value=get_post_meta($post->ID,'saf_'.$key,true);
    echo '<tr><th style="text-align:left;width:180px;padding:8px;border-bottom:1px solid #ddd">'.esc_html($label).'</th><td style="padding:8px;border-bottom:1px solid #ddd">'.nl2br(esc_html($value)).'</td></tr>';
  }
  echo '</table>';
}

function saftrade_request_form($context='contact',$compact=false){
  $interest=isset($_GET['flower']) ? sanitize_text_field(wp_unslash($_GET['flower'])) : '';
  ob_start(); ?>
  <form class="saf-form saf-ajax-form <?php echo $compact?'saf-form-compact':''; ?>" method="post" novalidate>
    <input type="hidden" name="action" value="saftrade_submit_request">
    <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('saftrade_request')); ?>">
    <input type="hidden" name="source" value="<?php echo esc_attr($context); ?>">
    <input class="saf-hp" aria-hidden="true" tabindex="-1" autocomplete="off" name="website" value="">
    <div class="saf-form-grid"><label>Ім’я *<input required name="name" autocomplete="name" placeholder="Ваше ім’я"></label><label>Компанія<input name="company" autocomplete="organization" placeholder="Назва компанії"></label></div>
    <div class="saf-form-grid"><label>E-mail *<input required type="email" name="email" autocomplete="email" placeholder="name@company.com"></label><label>Телефон<input name="phone" autocomplete="tel" placeholder="+380 ..."></label></div>
    <div class="saf-form-grid"><label>Місто доставки<input name="city" placeholder="Місто / населений пункт"></label><label>Квіти / категорія<input name="interest" value="<?php echo esc_attr($interest); ?>" placeholder="Троянди, тюльпани..."></label></div>
    <label>Опишіть запит *<textarea required name="message" rows="<?php echo $compact?4:6; ?>" placeholder="Позиції, приблизний обсяг, частота поставок і бажана дата."></textarea></label>
    <label class="saf-consent"><input required type="checkbox" name="consent" value="1"><span>Погоджуюся на обробку даних виключно для відповіді на цей B2B-запит.</span></label>
    <button class="saf-btn" type="submit"><span class="saf-submit-label">Надіслати запит</span><span aria-hidden="true">→</span></button>
    <div class="saf-form-status" role="status" aria-live="polite"></div>
  </form><?php
  return ob_get_clean();
}
function saftrade_contact_shortcode(){ return saftrade_request_form('contact',false); }
add_shortcode('saftrade_contact','saftrade_contact_shortcode');

function saftrade_submit_request(){
  $nonce=sanitize_text_field(wp_unslash($_POST['nonce']??''));
  if(!wp_verify_nonce($nonce,'saftrade_request')) wp_send_json_error(['message'=>'Сесію форми завершено. Оновіть сторінку.'],403);
  if(!empty($_POST['website'])) wp_send_json_success(['message'=>'Дякуємо.']);
  $name=sanitize_text_field(wp_unslash($_POST['name']??''));
  $company=sanitize_text_field(wp_unslash($_POST['company']??''));
  $email=sanitize_email(wp_unslash($_POST['email']??''));
  $phone=sanitize_text_field(wp_unslash($_POST['phone']??''));
  $city=sanitize_text_field(wp_unslash($_POST['city']??''));
  $interest=sanitize_text_field(wp_unslash($_POST['interest']??''));
  $message=sanitize_textarea_field(wp_unslash($_POST['message']??''));
  $source=sanitize_text_field(wp_unslash($_POST['source']??'website'));
  $consent=isset($_POST['consent']) && $_POST['consent']==='1';
  if(!$name || !is_email($email) || !$message || !$consent) wp_send_json_error(['message'=>'Заповніть ім’я, коректний e-mail, повідомлення та підтвердьте згоду.'],422);

  $title=trim(($company ?: $name).' — '.($interest ?: 'Оптовий запит'));
  $id=wp_insert_post(['post_type'=>'saf_inquiry','post_status'=>'private','post_title'=>wp_strip_all_tags($title)]);
  if($id && !is_wp_error($id)){
    foreach(compact('name','company','email','phone','city','interest','message','source') as $key=>$value) update_post_meta($id,'saf_'.$key,$value);
    update_post_meta($id,'saf_received_at',current_time('mysql'));
  }
  $subject='Новий оптовий запит — '.($company ?: $name);
  $body="Ім’я: {$name}\nКомпанія: {$company}\nE-mail: {$email}\nТелефон: {$phone}\nМісто: {$city}\nІнтерес: {$interest}\nДжерело: {$source}\n\nЗапит:\n{$message}";
  $headers=['Reply-To: '.$name.' <'.$email.'>'];
  wp_mail(get_option('admin_email'),$subject,$body,$headers);
  wp_send_json_success(['message'=>'Дякуємо! Запит збережено. Ми зв’яжемося з вами після перевірки деталей.']);
}
add_action('wp_ajax_saftrade_submit_request','saftrade_submit_request');
add_action('wp_ajax_nopriv_saftrade_submit_request','saftrade_submit_request');

function saftrade_inquiry_columns($cols){
  return ['cb'=>$cols['cb'],'title'=>'Запит','saf_contact'=>'Контакт','saf_interest'=>'Інтерес','date'=>'Дата'];
}
add_filter('manage_saf_inquiry_posts_columns','saftrade_inquiry_columns');
function saftrade_inquiry_column($col,$id){
  if($col==='saf_contact') echo esc_html(get_post_meta($id,'saf_name',true).' · '.get_post_meta($id,'saf_email',true));
  if($col==='saf_interest') echo esc_html(get_post_meta($id,'saf_interest',true));
}
add_action('manage_saf_inquiry_posts_custom_column','saftrade_inquiry_column',10,2);

function saftrade_customize($wp_customize){
  $wp_customize->add_section('saf_brand',['title'=>'SAF Trade — бренд і контакти','priority'=>30]);
  $fields=[
    'saf_company'=>['Назва компанії','ТОВ «САФ ТРЕЙД УКРАЇНА»','text'],
    'saf_hero_title'=>['Головний заголовок','Свіжі квіти для бізнесу. Надійне оптове постачання.','text'],
    'saf_hero_text'=>['Текст на головній','Добираємо та постачаємо зрізані квіти для флористів, магазинів, декораторів і дистриб’юторів.','textarea'],
    'saf_phone'=>['Телефон','','text'],
    'saf_email'=>['E-mail','','email'],
    'saf_address'=>['Адреса / регіон','','text'],
    'saf_hours'=>['Графік','','text'],
    'saf_instagram'=>['Instagram URL','','url'],
    'saf_facebook'=>['Facebook URL','','url']
  ];
  foreach($fields as $id=>$cfg){
    $sanitize=$cfg[2]==='email'?'sanitize_email':($cfg[2]==='url'?'esc_url_raw':($cfg[2]==='textarea'?'sanitize_textarea_field':'sanitize_text_field'));
    $wp_customize->add_setting($id,['default'=>$cfg[1],'sanitize_callback'=>$sanitize]);
    $wp_customize->add_control($id,['label'=>$cfg[0],'section'=>'saf_brand','type'=>$cfg[2]]);
  }
  $wp_customize->add_setting('saf_accent',['default'=>'#8a3f52','sanitize_callback'=>'sanitize_hex_color']);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize,'saf_accent',['label'=>'Акцентний колір','section'=>'saf_brand']));
}
add_action('customize_register','saftrade_customize');

function saftrade_css_vars(){
  $accent=get_theme_mod('saf_accent','#8a3f52');
  echo '<style>:root{--accent:'.esc_attr($accent).'}</style>';
}
add_action('wp_head','saftrade_css_vars',20);

function saftrade_head_meta(){
  if(is_admin()) return;
  $desc='ТОВ «САФ ТРЕЙД УКРАЇНА» — оптовий каталог свіжих квітів, комплектація B2B-замовлень і логістика.';
  if(is_page()){
    $slug=get_post_field('post_name',get_queried_object_id());
    $page_descs=[
      'zakupivlya-dostavka-kvitiv-optom'=>'Як проходить оптова закупівля та доставка квітів: специфікація, доступність, пакування, комплектація, логістика та регулярні B2B-поставки.',
      'yak-zamovyty-kvity-optom'=>'Покроково: як підготувати оптовий запит на квіти, які параметри вказати, як погоджується специфікація та доставка.',
      'svizhist-ta-yakist'=>'Свіжість і якість квітів у B2B-поставці: погодження характеристик партії, пакування, сезонність та контроль специфікації.',
      'faq-optovi-kvity'=>'Відповіді на типові питання про оптові квіти: ціни, наявність, мінімальні обсяги, заміни, доставка та регулярні поставки.',
      'postachannya-pohodzhennya-kvitiv'=>'Як SAF Trade підходить до джерела партії та походження квітів: нейтрально, без непідтверджених країн чи постачальників; дані уточнюються під конкретну позицію.'
    ];
    if(isset($page_descs[$slug])) $desc=$page_descs[$slug];
  }
  if(is_singular() && has_excerpt()) $desc=wp_strip_all_tags(get_the_excerpt());
  echo '<meta name="description" content="'.esc_attr(wp_trim_words($desc,28,'')).'">' . "\n";
  echo '<meta property="og:type" content="'.(is_singular()?'article':'website').'">' . "\n";
  echo '<meta property="og:title" content="'.esc_attr(wp_get_document_title()).'">' . "\n";
  echo '<meta property="og:description" content="'.esc_attr(wp_trim_words($desc,28,'')).'">' . "\n";
  echo '<meta property="og:image" content="'.esc_url(saftrade_placeholder_image('hero')).'">' . "\n";
  $schema=['@context'=>'https://schema.org','@type'=>'Organization','name'=>'LLC SAF TRADE UKRAINE','alternateName'=>'ТОВ «САФ ТРЕЙД УКРАЇНА»','url'=>home_url('/')];
  $email=get_theme_mod('saf_email',''); $phone=get_theme_mod('saf_phone','');
  if($email) $schema['email']=$email; if($phone) $schema['telephone']=$phone;
  echo '<script type="application/ld+json">'.wp_json_encode($schema,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>' . "\n";
  if(is_page('faq-optovi-kvity')){
    $faq=['@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>[
      ['@type'=>'Question','name'=>'Чи є ціни на сайті фіксованими?','acceptedAnswer'=>['@type'=>'Answer','text'=>'Ні. Актуальна ціна залежить від сезону, доступності, сорту, параметрів партії та обсягу і підтверджується під конкретний запит.']],
      ['@type'=>'Question','name'=>'Чи весь асортимент із каталогу завжди є в наявності?','acceptedAnswer'=>['@type'=>'Answer','text'=>'Каталог показує напрямок асортименту. Фактична наявність і характеристики перевіряються перед формуванням пропозиції.']],
      ['@type'=>'Question','name'=>'Яке мінімальне замовлення?','acceptedAnswer'=>['@type'=>'Answer','text'=>'Мінімальний обсяг залежить від позиції та формату поставки і підтверджується у відповіді на запит.']],
      ['@type'=>'Question','name'=>'Як відбувається доставка?','acceptedAnswer'=>['@type'=>'Answer','text'=>'Маршрут, строки, спосіб і вартість доставки залежать від міста, обсягу та конкретної партії й погоджуються перед відправленням.']]
    ]];
    echo '<script type="application/ld+json">'.wp_json_encode($faq,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>' . "\n";
  }
}
add_action('wp_head','saftrade_head_meta',3);

function saftrade_fallback_menu(){
  echo '<ul class="saf-menu-list">';
  $items=[
    ['Головна',home_url('/')],
    ['Каталог',saftrade_catalog_url()],
    ['Про компанію',saftrade_page_url('pro-kompaniyu')],
    ['Оптовим клієнтам',saftrade_page_url('optom')],
    ['Доставка',saftrade_page_url('dostavka')],
    ['Контакти',saftrade_page_url('kontakty')]
  ];
  foreach($items as $item) echo '<li><a href="'.esc_url($item[1]).'">'.esc_html($item[0]).'</a></li>';
  echo '</ul>';
}

function saftrade_upsert_primary_menu($ids){
  $locations=get_theme_mod('nav_menu_locations',[]);
  $menu_id=!empty($locations['primary']) ? (int)$locations['primary'] : 0;
  if(!$menu_id){
    $menu=wp_get_nav_menu_object('SAF Trade Main Menu');
    if($menu) $menu_id=(int)$menu->term_id;
  }
  if(!$menu_id){
    $created=wp_create_nav_menu('SAF Trade Main Menu');
    if(!is_wp_error($created)) $menu_id=(int)$created;
  }
  if(!$menu_id) return;

  $existing=wp_get_nav_menu_items($menu_id);
  if(!is_array($existing)) $existing=[];
  $by_title=[];
  foreach($existing as $item) $by_title[mb_strtolower(trim(wp_strip_all_tags($item->title)))]=$item;

  $expected=[
    'Головна'=>['menu-item-title'=>'Головна','menu-item-url'=>home_url('/'),'menu-item-type'=>'custom','menu-item-status'=>'publish'],
    'Каталог'=>['menu-item-title'=>'Каталог','menu-item-url'=>saftrade_catalog_url(),'menu-item-type'=>'custom','menu-item-status'=>'publish']
  ];
  foreach(['pro-kompaniyu'=>'Про компанію','optom'=>'Оптовим клієнтам','dostavka'=>'Доставка','kontakty'=>'Контакти'] as $slug=>$label){
    if(!empty($ids[$slug])){
      $expected[$label]=[
        'menu-item-title'=>$label,
        'menu-item-object'=>'page',
        'menu-item-object-id'=>(int)$ids[$slug],
        'menu-item-type'=>'post_type',
        'menu-item-status'=>'publish'
      ];
    }
  }
  foreach($expected as $label=>$args){
    $key=mb_strtolower($label);
    $item_id=isset($by_title[$key]) ? (int)$by_title[$key]->ID : 0;
    wp_update_nav_menu_item($menu_id,$item_id,$args);
  }
  $locations['primary']=$menu_id;
  set_theme_mod('nav_menu_locations',$locations);
}

function saftrade_seed_site(){
  saftrade_register_content();

  $pages=[
    'pro-kompaniyu'=>[
      'Про компанію',
      '<p class="saf-prose-intro"><strong>ТОВ «САФ ТРЕЙД УКРАЇНА» / LLC SAF TRADE UKRAINE</strong> — компанія, що розвиває B2B-напрям оптової торгівлі квітами для професійних покупців. Сайт створений для квіткових магазинів, флористичних студій, декораторів, торгових мереж, дистриб’юторів та інших бізнес-клієнтів.</p>
      <h2>Що є основою нашого підходу</h2>
      <p>Ми будуємо комунікацію навколо конкретного запиту покупця: категорія або назва квітів, бажані параметри, приблизний обсяг, пакування, місто та потрібна дата. Це дозволяє відокремити орієнтовний каталог від фактичної пропозиції по конкретній партії.</p>
      <div class="saf-info-grid">
        <div class="saf-info-card"><b>01</b><h3>B2B-фокус</h3><p>Сайт і форми орієнтовані на професійні закупівлі, повторні замовлення та роботу зі специфікаціями.</p></div>
        <div class="saf-info-card"><b>02</b><h3>Прозорі параметри</h3><p>Наявність, сорт, відтінок, довжина, пакування та інші критичні характеристики підтверджуються під конкретний запит.</p></div>
        <div class="saf-info-card"><b>03</b><h3>Без непідтверджених обіцянок</h3><p>Ми не вказуємо країни походження, постачальників, строки чи комерційні умови як факт, якщо вони ще не підтверджені.</p></div>
        <div class="saf-info-card"><b>04</b><h3>Масштабований каталог</h3><p>Категорії й картки квітів побудовані на багаторазових шаблонах, тому асортимент можна розширювати без нового дизайну.</p></div>
      </div>
      <h2>Для кого працює сайт</h2>
      <p>Структура SAF Trade підходить для бізнесів, яким потрібні разові або регулярні оптові запити: роздрібних квіткових точок, флористичних майстерень, декораторів, компаній з подієвими проєктами, мережевих покупців та дистриб’юторів.</p>
      <h2>Що ще потребує підтвердження компанією</h2>
      <p>Юридична адреса, конкретні регіони поставок, історія компанії, підтверджені країни або канали закупівлі, фіксований графік роботи та інші корпоративні факти додаються лише після підтвердження власником сайту.</p>
      <div class="saf-info-note"><strong>Компанія:</strong> ТОВ «САФ ТРЕЙД УКРАЇНА» / LLC SAF TRADE UKRAINE. Інша фактична корпоративна інформація публікується після підтвердження клієнтом.</div>'
    ],
    'postachannya-pohodzhennya-kvitiv'=>[
      'Постачання та походження квітів',
      '<p class="saf-prose-intro">Для оптової партії важливо розрізняти <strong>каталожну категорію</strong> та <strong>фактично підтверджене джерело конкретної партії</strong>. SAF Trade не публікує неперевірені країни, аукціони, господарства, постачальників або імпортні схеми лише для заповнення сторінки.</p>
      <h2>Як формується інформація про походження</h2>
      <p>Походження й канал постачання можуть відрізнятися залежно від виду квітів, сорту, сезону, поточної доступності та конкретної партії. Якщо ця інформація є важливою для закупівлі, її слід зазначити у запиті; підтверджені дані можуть бути включені до комерційної пропозиції або специфікації.</p>
      <div class="saf-content-steps">
        <div><span>01</span><h3>Потреба покупця</h3><p>Категорія, сорт, колір, параметри, кількість і дата.</p></div>
        <div><span>02</span><h3>Актуальна доступність</h3><p>Перевіряються позиції, які можна запропонувати під конкретний запит.</p></div>
        <div><span>03</span><h3>Підтверджені дані</h3><p>Походження або джерело вказується лише тоді, коли воно відоме для конкретної партії.</p></div>
        <div><span>04</span><h3>Специфікація</h3><p>Критичні для покупця характеристики фіксуються до комплектації.</p></div>
      </div>
      <h2>Чому ми не перелічуємо країни «за замовчуванням»</h2>
      <p>Такий перелік міг би створити хибне враження, що кожна позиція завжди надходить із конкретної країни або від конкретного партнера. До отримання підтверджених корпоративних даних сайт використовує нейтральне формулювання — <em>«походження / джерело партії уточнюється під конкретне замовлення»</em>.</p>
      <h2>Що можна вказати в запиті</h2>
      <ul><li>чи критична конкретна країна або регіон походження;</li><li>потрібний сорт або комерційний клас;</li><li>вимоги до довжини, кольору, розміру або пакування;</li><li>орієнтовний обсяг і регулярність закупівлі;</li><li>місто та бажану дату отримання.</li></ul>
      <div class="saf-info-note"><strong>Нейтральний принцип:</strong> сайт не вигадує джерела поставки. Дані про походження публікуються або передаються покупцю після фактичного підтвердження.</div>'
    ],
    'optom'=>[
      'Оптовим клієнтам',
      '<p class="saf-prose-intro">Оптова робота починається з B2B-запиту, а не з фіксованої роздрібної ціни. Чим точніше описані квіти, кількість, параметри та логістика, тим точніше можна підготувати пропозицію.</p>
      <h2>Як проходить запит і замовлення</h2>
      <div class="saf-content-steps">
        <div><span>01</span><h3>Запит</h3><p>Покупець передає назви або категорії квітів, приблизні кількості, дату та місто.</p></div>
        <div><span>02</span><h3>Перевірка</h3><p>Уточнюється актуальна доступність, характеристики партії та можливі альтернативи.</p></div>
        <div><span>03</span><h3>Пропозиція</h3><p>Погоджуються позиції, ціна за запитом, кількість, пакування та логістичні параметри.</p></div>
        <div><span>04</span><h3>Комплектація</h3><p>Після підтвердження специфікації партія готується до передачі в логістику.</p></div>
      </div>
      <h2>Ціна — за конкретним запитом</h2>
      <p>На сайті не публікуються універсальні фіксовані оптові ціни, оскільки фактична пропозиція може залежати від виду квітів, сорту, сезону, характеристик партії, обсягу, формату пакування та логістики. Покупець отримує актуальну комерційну інформацію у відповіді на запит.</p>
      <h2>Кількість і мінімальний обсяг</h2>
      <p>Мінімальний обсяг не задається одним числом для всього каталогу без підтверджених умов. Він може відрізнятися між позиціями та форматами поставки. У заявці достатньо вказати бажану кількість — доступний формат буде уточнений у пропозиції.</p>
      <h2>Специфікація партії</h2>
      <ul><li>категорія, назва або бажаний сорт;</li><li>відтінок і важливі візуальні параметри;</li><li>довжина стебла або інші характеристики, якщо вони критичні;</li><li>кількість і бажаний формат пакування;</li><li>місто та бажана дата;</li><li>потреба в регулярних повторних поставках;</li><li>допустимі або недопустимі заміни.</li></ul>
      <h2>Пакування</h2>
      <p>Формат пакування погоджується відповідно до типу квітів, обсягу та способу транспортування. Якщо потрібен конкретний формат, кількість у пачці чи коробці або інша вимога, її варто вказати у запиті.</p>
      <h2>Заміни та недоступні позиції</h2>
      <p>Якщо потрібної позиції немає, альтернатива не повинна підставлятися автоматично. Можливий варіант узгоджується з покупцем за категорією, кольором, параметрами або бюджетом.</p>
      <h2>Регулярні замовлення</h2>
      <p>Для повторних закупівель можна сформувати типовий список категорій, бажані параметри й орієнтовну частоту. Це спрощує наступні запити, але актуальна наявність і комерційні умови все одно підтверджуються для конкретної партії.</p>
      <div class="saf-info-note"><strong>Важливо:</strong> ціна, доступність, мінімальна кількість, походження, точне пакування та логістика підтверджуються до виконання конкретного замовлення.</div>'
    ],
    'dostavka'=>[
      'Доставка та логістика',
      '<p class="saf-prose-intro">Логістика зрізаних квітів потребує чіткого погодження партії, підготовки до транспортування та своєчасної комунікації. SAF Trade не публікує непідтверджені універсальні строки, регіони чи тарифи — параметри доставки уточнюються під замовлення.</p>
      <h2>1. Підготовка замовлення</h2>
      <p>Перед відправленням звіряється погоджена специфікація: позиції, кількість, ключові характеристики та дані, важливі для покупця. Якщо під час підготовки потрібне уточнення або заміна, це має бути погоджено до передачі партії в логістику.</p>
      <h2>2. Пакування</h2>
      <p>Пакування підбирається з урахуванням типу квітів, обсягу та майбутнього транспортування. Точний формат, кількість у пачці/коробці та інші параметри не встановлюються на сайті як універсальні, якщо вони не підтверджені для конкретної позиції.</p>
      <h2>3. Маршрут і транспорт</h2>
      <p>Спосіб транспортування, маршрут, перевізник та можливі проміжні етапи залежать від конкретного замовлення. Вони погоджуються після того, як відомі місто отримання, обсяг, склад партії та бажана дата.</p>
      <h2>4. Комунікація та статус</h2>
      <p>Покупець має отримувати актуальну інформацію про готовність партії, погоджене відправлення та зміни, якщо вони виникають. Сайт не обіцяє автоматичний онлайн-трекінг, якщо така система ще не підтверджена клієнтом.</p>
      <h2>5. Отримання</h2>
      <p>Після доставки професійному покупцю важливо перевірити відповідність погодженій специфікації та належно організувати подальше зберігання/підготовку квітів відповідно до внутрішніх процедур бізнесу.</p>
      <div class="saf-info-grid">
        <div class="saf-info-card"><b>01</b><h3>Підготовка</h3><p>Звірення погодженого складу партії.</p></div>
        <div class="saf-info-card"><b>02</b><h3>Пакування</h3><p>Під конкретний тип квітів і формат транспортування.</p></div>
        <div class="saf-info-card"><b>03</b><h3>Маршрут</h3><p>Умови погоджуються після отримання даних про замовлення.</p></div>
        <div class="saf-info-card"><b>04</b><h3>Статус</h3><p>Комунікація щодо готовності, відправлення та змін.</p></div>
      </div>
      <div class="saf-info-note"><strong>Не публікуємо без підтвердження:</strong> точні дні доставки, країни/регіони обслуговування, митні процедури, перевізників і тарифи.</div>'
    ],
    'zakupivlya-dostavka-kvitiv-optom'=>[
      'Закупівля та доставка квітів оптом',
      '<p class="saf-prose-intro">Оптова закупівля квітів — це процес від формування потреби до отримання узгодженої партії. Для професійного покупця важливо зафіксувати не тільки назву квітки, а й параметри, доступність, допустимі заміни, пакування та логістику.</p>
      <h2>Від потреби бізнесу до специфікації</h2>
      <p>Запит може бути точним списком або описом задачі. Наприклад: асортимент для квіткового магазину, сезонна добірка, палітра для події чи регулярна B2B-закупівля. На старті корисно вказати категорії, приблизні кількості, бажані кольори/параметри, місто та дату.</p>
      <div class="saf-content-steps">
        <div><span>01</span><h3>Формування запиту</h3><p>Позиції, параметри, приблизний обсяг, місто та дата.</p></div>
        <div><span>02</span><h3>Перевірка доступності</h3><p>Уточнюються фактичні позиції та можливі альтернативи.</p></div>
        <div><span>03</span><h3>Погодження</h3><p>Фіксуються ціна за запитом, характеристики, пакування та логістика.</p></div>
        <div><span>04</span><h3>Виконання</h3><p>Партія комплектується та передається в погоджений логістичний процес.</p></div>
      </div>
      <h2>Звідки походять квіти</h2>
      <p>Джерело конкретної партії може залежати від категорії, сорту, сезону та поточної доступності. SAF Trade не вказує країни, аукціони, господарства чи постачальників як постійний факт без підтверджених даних. Якщо походження важливе для покупця, цей параметр включається в запит і уточнюється під конкретну пропозицію.</p>
      <h2>Що входить у специфікацію</h2>
      <p>За можливості фіксуються: категорія/сорт, колір, довжина стебла, формат пакування, кількість, сезонність або доступність, походження (якщо підтверджено), допустимі заміни та логістичні параметри.</p>
      <h2>Ціна й доступність</h2>
      <p>Каталог не є прайс-листом із гарантованою наявністю. Оптові ціни та доступність підтверджуються за конкретним запитом, щоб уникнути застарілих або неточних даних.</p>
      <h2>Комплектація, пакування і доставка</h2>
      <p>Після погодження партія готується до відправлення. Формат пакування, маршрут, транспорт, орієнтовний графік та вартість логістики залежать від замовлення і уточнюються окремо.</p>
      <h2>Регулярні поставки</h2>
      <p>Постійні B2B-клієнти можуть використовувати типовий перелік категорій і параметрів для повторних запитів. Це не означає автоматично гарантовану наявність: кожна наступна партія підтверджується за актуальними даними.</p>
      <div class="saf-info-note"><strong>Принцип:</strong> краще залишити параметр «за запитом», ніж опублікувати непідтверджену країну, ціну, мінімальну кількість або строк.</div>'
    ],
    'yak-zamovyty-kvity-optom'=>[
      'Як замовити квіти оптом',
      '<p class="saf-prose-intro">Чим точніший початковий запит, тим швидше можна підготувати релевантну B2B-пропозицію без зайвих уточнень.</p>
      <h2>1. Опишіть задачу або список</h2><p>Можна вказати конкретні назви/категорії або описати потребу: магазин, флористична студія, декор, подія чи регулярний асортимент.</p>
      <h2>2. Додайте ключові параметри</h2><ul><li>категорії або бажані сорти;</li><li>кольори та інші критичні характеристики;</li><li>орієнтовну кількість або бюджет;</li><li>бажаний формат пакування, якщо він важливий;</li><li>місто та бажану дату;</li><li>регулярність поставки;</li><li>вимоги до походження, якщо це критично.</li></ul>
      <h2>3. Отримайте актуальну пропозицію</h2><p>Перевіряються доступні позиції. Ціна, фактична наявність, мінімальна кількість та інші комерційні параметри підтверджуються під конкретний запит.</p>
      <h2>4. Узгодьте альтернативи</h2><p>Якщо окрема позиція недоступна, заміна пропонується для погодження. Вона не повинна змінюватися автоматично без підтвердження покупця.</p>
      <h2>5. Погодьте специфікацію</h2><p>Перед виконанням фіксуються ключові характеристики, кількість, пакування та логістичні параметри.</p>
      <h2>6. Комплектація й доставка</h2><p>Партія готується відповідно до погоджених даних. Точний маршрут, транспорт, графік і вартість доставки уточнюються для конкретного замовлення.</p>
      <div class="saf-info-note"><strong>Швидкий спосіб:</strong> форма «Запросити ціни» надсилає B2B-запит нашій команді для опрацювання.</div>'
    ],
    'svizhist-ta-yakist'=>[
      'Свіжість і якість квітів у B2B-поставці',
      '<p class="saf-prose-intro">Для професійного квіткового бізнесу якість — це узгоджені характеристики конкретної партії, акуратне пакування, логістика та своєчасна комунікація.</p>
      <h2>Що перевіряється у специфікації</h2><p>За наявності підтверджених даних уточнюються сорт/клас, колір, довжина, формат пакування, кількість, походження та доступність. Якщо параметр не підтверджений, він не публікується як гарантований.</p>
      <div class="saf-info-grid"><div class="saf-info-card"><b>01</b><h3>Відповідність запиту</h3><p>Партія формується за погодженим переліком.</p></div><div class="saf-info-card"><b>02</b><h3>Пакування</h3><p>Формат узгоджується з типом квітів і транспортуванням.</p></div><div class="saf-info-card"><b>03</b><h3>Природні відмінності</h3><p>Колір, розмір та інші параметри живого товару можуть відрізнятися між партіями.</p></div><div class="saf-info-card"><b>04</b><h3>Комунікація</h3><p>Критичні зміни або альтернативи погоджуються до виконання.</p></div></div>
      <h2>Сезонність</h2><p>Частина асортименту має сезонний характер. Тому фото й опис у каталозі є орієнтиром, а фактична доступність підтверджується під запит.</p>
      <h2>Фото в каталозі</h2><p>Зображення допомагають зрозуміти категорію та загальний характер квітів, але не є гарантією точного відтінку або конкретної партії. Для критичних параметрів необхідне окреме підтвердження.</p>'
    ],
    'faq-optovi-kvity'=>[
      'Питання про оптові квіти',
      '<p class="saf-prose-intro">Відповіді на типові питання професійних покупців перед першим або регулярним B2B-запитом.</p>
      <div class="saf-faq-list">
        <details open><summary>Чому на сайті немає фіксованого прайс-листа?</summary><p>Актуальна оптова пропозиція може залежати від позиції, сезону, партії, обсягу, пакування та логістики. Ціна підтверджується під конкретний запит.</p></details>
        <details><summary>Чи весь каталог завжди є в наявності?</summary><p>Ні. Каталог показує структуру та напрямок асортименту; фактична доступність перевіряється перед пропозицією.</p></details>
        <details><summary>Яке мінімальне замовлення?</summary><p>Без підтверджених загальних правил сайт не публікує єдине число. Доступний мінімальний формат уточнюється для конкретної позиції та поставки.</p></details>
        <details><summary>Як дізнатися походження квітів?</summary><p>Якщо цей параметр важливий, вкажіть його в запиті. Походження підтверджується для конкретної партії; сайт не вигадує країни чи постачальників.</p></details>
        <details><summary>Чи можна погодити заміну?</summary><p>Так. Якщо позиція недоступна, альтернатива пропонується окремо й використовується лише після погодження.</p></details>
        <details><summary>Як працюють регулярні замовлення?</summary><p>Можна сформувати типовий список категорій, параметрів і приблизну частоту; кожна партія все одно підтверджується за актуальною доступністю.</p></details>
        <details><summary>Як відбувається доставка?</summary><p>Підготовка, пакування, маршрут і транспорт погоджуються під замовлення. Непідтверджені універсальні строки, регіони чи тарифи не публікуються.</p></details>
        <details><summary>Чи можна замовити для події?</summary><p>Так. У запиті корисно вказати дату, палітру, категорії, приблизний обсяг, місто та критичні параметри.</p></details>
      </div>'
    ],
    'kontakty'=>[
      'Контакти',
      '<p class="saf-prose-intro">Надішліть B2B-запит із потрібними категоріями, приблизним обсягом, містом і бажаною датою. Підтверджені контактні дані компанії відображаються на сайті після внесення власником у налаштування теми.</p>[saftrade_contact]'
    ]
  ];

  $ids=[];
  foreach($pages as $slug=>$data){
    $page=get_page_by_path($slug,OBJECT,'page');
    if(!$page){
      $id=wp_insert_post([
        'post_type'=>'page',
        'post_status'=>'publish',
        'post_title'=>$data[0],
        'post_name'=>$slug,
        'post_content'=>$data[1],
        'comment_status'=>'closed','ping_status'=>'closed'
      ]);
    } else {
      $id=$page->ID;
      if($page->post_status!=='publish') wp_update_post(['ID'=>$id,'post_status'=>'publish']);
      $managed=get_post_meta($id,'_saftrade_managed_page',true);
      $old_seed=(string)get_option('saftrade_seed_version','');
      $looks_managed=(strpos((string)$page->post_content,'saf-prose-intro')!==false || trim((string)$page->post_content)==='' || $managed==='1');
      if($looks_managed && $old_seed!==SAFTRADE_VERSION){
        wp_update_post(['ID'=>$id,'post_content'=>$data[1],'comment_status'=>'closed','ping_status'=>'closed']);
      }
    }
    if($id && !is_wp_error($id)) update_post_meta($id,'_saftrade_managed_page','1');
    if($id && !is_wp_error($id)) $ids[$slug]=(int)$id;
  }

  $home=get_page_by_path('golovna',OBJECT,'page');
  if(!$home){
    $home_id=wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>'Головна','post_name'=>'golovna','post_content'=>'','comment_status'=>'closed','ping_status'=>'closed']);
  } else {
    $home_id=$home->ID;
    if($home->post_status!=='publish') wp_update_post(['ID'=>$home_id,'post_status'=>'publish']);
  }
  if($home_id && !is_wp_error($home_id)){
    update_option('show_on_front','page');
    update_option('page_on_front',(int)$home_id);
  }

  $terms=[
    'troiandy'=>'Троянди',
    'tyulpany'=>'Тюльпани',
    'khrizantemy'=>'Хризантеми',
    'sonyashnyky'=>'Соняшники',
    'sezonni'=>'Сезонні квіти'
  ];
  $term_ids=[];
  foreach($terms as $slug=>$name){
    $term=get_term_by('slug',$slug,'flower_category');
    if(!$term){
      $created=wp_insert_term($name,'flower_category',['slug'=>$slug]);
      if(!is_wp_error($created)) $term=get_term((int)$created['term_id'],'flower_category');
    }
    if($term && !is_wp_error($term)) $term_ids[$slug]=(int)$term->term_id;
  }

  $products=[
    ['Троянда — світлі відтінки','troiandy','Каталожний приклад світлих троянд для професійного B2B-запиту.','За конкретною партією','За запитом','За запитом','За запитом','світлі відтінки','За запитом',['White Avalanche'],'catalog-rose-light.jpg'],
    ['Троянда — червоні відтінки','troiandy','Каталожний приклад червоних троянд; фактичний сорт і параметри підтверджуються під запит.','За конкретною партією','За запитом','За запитом','За запитом','червоні відтінки','За запитом',['Red Naomi'],'catalog-rose-red.jpg'],
    ['Тюльпани — сезонна добірка','tyulpany','Сезонна категорія тюльпанів для магазинів, флористики та подій.','За конкретною партією','За запитом','За запитом','Сезонно / за запитом','різні відтінки','За запитом',['Pink Tulip'],'catalog-tulips.jpg'],
    ['Хризантема — оптова категорія','khrizantemy','Категорія хризантем для професійного асортименту; конкретний сорт підтверджується окремо.','За конкретною партією','За запитом','За запитом','За запитом','різні відтінки','За запитом',['Chrysanthemum Magnum','Chrysanthemum Baltica'],'catalog-mums.jpg'],
    ['Соняшник — сезонна категорія','sonyashnyky','Сезонна категорія соняшників для акцентних композицій і B2B-поставок.','За конкретною партією','За запитом','За запитом','Сезонно / за запитом','жовті відтінки','За запитом',['Sunrich Sunflower'],'catalog-sunflower.jpg'],
    ['Сезонна добірка','sezonni','Змінна добірка сезонних позицій; склад і параметри підтверджуються під конкретний запит.','За конкретною партією','За запитом','За специфікацією','Сезонно / за запитом','мікс','За позицією',['Seasonal Mix'],'catalog-seasonal.jpg']
  ];

  foreach($products as $p){
    $existing=get_page_by_title($p[0],OBJECT,'flower');
    $was_legacy=false;
    if(!$existing && !empty($p[9])){
      foreach((array)$p[9] as $legacy_title){
        $legacy=get_page_by_title($legacy_title,OBJECT,'flower');
        if($legacy){
          wp_update_post(['ID'=>$legacy->ID,'post_title'=>$p[0],'post_excerpt'=>$p[2],'comment_status'=>'closed','ping_status'=>'closed']);
          $existing=get_post($legacy->ID);
          $was_legacy=true;
          break;
        }
      }
    }
    if(!$existing){
      $pid=wp_insert_post([
        'post_type'=>'flower','post_status'=>'publish','post_title'=>$p[0],'post_excerpt'=>$p[2],
        'post_content'=>'<p>'.$p[2].'</p><p>Фото, наявність, походження, сорт, відтінок, довжина, пакування та інші параметри підтверджуються перед замовленням.</p>',
        'comment_status'=>'closed','ping_status'=>'closed'
      ]);
    } else {
      $pid=$existing->ID;
      if($existing->post_status!=='publish') wp_update_post(['ID'=>$pid,'post_status'=>'publish']);
    }
    if($pid && !is_wp_error($pid)){
      if(isset($term_ids[$p[1]])) wp_set_object_terms($pid,[$term_ids[$p[1]]],'flower_category',false);
      $seeded=get_post_meta($pid,'_saftrade_seeded_flower',true)==='1' || $was_legacy;
      update_post_meta($pid,'_saftrade_seeded_flower','1');
      update_post_meta($pid,'_saftrade_demo_image',sanitize_file_name((string)$p[10]));
      wp_update_post(['ID'=>$pid,'comment_status'=>'closed','ping_status'=>'closed']);
      $values=['saf_origin'=>$p[3],'saf_grade'=>$p[4],'saf_pack'=>$p[5],'saf_availability'=>$p[6],'saf_colors'=>$p[7],'saf_stem'=>$p[8],'saf_moq'=>'За запитом'];
      foreach($values as $k=>$v){
        $current=(string)get_post_meta($pid,$k,true);
        if($current==='' || $seeded || $was_legacy) update_post_meta($pid,$k,$v);
      }
      if($seeded || $was_legacy){
        wp_update_post(['ID'=>$pid,'post_excerpt'=>$p[2],'post_content'=>'<p>'.$p[2].'</p><p>Фото, наявність, походження, сорт, відтінок, довжина, пакування та інші параметри підтверджуються перед замовленням.</p>']);
      }
    }
  }

  $all_seeded=get_posts(['post_type'=>'flower','post_status'=>'any','posts_per_page'=>-1,'meta_key'=>'_saftrade_seeded_flower','meta_value'=>'1','fields'=>'ids']);
  foreach($all_seeded as $fid){ wp_update_post(['ID'=>$fid,'comment_status'=>'closed','ping_status'=>'closed']); }

  global $wpdb;
  $wpdb->query("UPDATE {$wpdb->posts} SET comment_status='closed', ping_status='closed' WHERE post_type IN ('page','flower')");

  saftrade_upsert_primary_menu($ids);

  $blogname=trim((string)get_option('blogname'));
  if($blogname==='' || preg_match('/^(robertmichaelis|my site|site title)$/i',$blogname)){
    update_option('blogname','SAF Trade Ukraine');
  }
  $tagline=trim((string)get_option('blogdescription'));
  if($tagline==='' || stripos($tagline,'just another')!==false){
    update_option('blogdescription','Оптові квіти для професійних покупців');
  }

  update_option('saftrade_seed_version',SAFTRADE_VERSION,false);
  flush_rewrite_rules(false);
}
add_action('after_switch_theme','saftrade_seed_site');

function saftrade_maybe_seed_site(){
  if(!current_user_can('manage_options')) return;
  if(get_option('saftrade_seed_version')!==SAFTRADE_VERSION){
    saftrade_seed_site();
  }
}
add_action('admin_init','saftrade_maybe_seed_site');

function saftrade_archive_all($query){
  if(!is_admin() && $query->is_main_query() && $query->is_post_type_archive('flower')) $query->set('posts_per_page',30);
}
add_action('pre_get_posts','saftrade_archive_all');
