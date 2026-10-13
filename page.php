<?php get_header(); ?>
<main id="content">
<?php while(have_posts()): the_post();
$slug=get_post_field('post_name',get_the_ID());
$image_key=['pro-kompaniyu'=>'about','postachannya-pohodzhennya-kvitiv'=>'sourcing','optom'=>'wholesale','dostavka'=>'delivery','zakupivlya-dostavka-kvitiv-optom'=>'market','yak-zamovyty-kvity-optom'=>'roses','svizhist-ta-yakist'=>'quality','faq-optovi-kvity'=>'faq'][$slug] ?? '';
?>
<section class="saf-page-hero">
  <div class="saf-container">
    <span class="saf-kicker">SAF TRADE UKRAINE</span>
    <h1><?php the_title(); ?></h1>
  </div>
</section>
<section class="saf-section saf-page-section">
  <div class="saf-container saf-page-grid">
    <article class="saf-prose">
      <?php if($image_key): ?>
      <figure class="saf-page-image">
        <img src="<?php echo esc_url(saftrade_image($image_key)); ?>" data-saf-fallback="<?php echo esc_url(saftrade_placeholder_image($image_key)); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
      </figure>
      <?php endif; ?>
      <?php the_content(); ?>
    </article>
    <aside class="saf-contact-card">
      <span class="saf-kicker saf-kicker-light">B2B ЗАПИТ</span>
      <h3>Потрібна пропозиція?</h3>
      <p>Надішліть список квітів, обсяг і місто доставки.</p>
      <button class="saf-btn saf-btn-cream" type="button" data-saf-quote>Запросити ціни</button>
      <div class="saf-contact-mini">
        <?php $email=get_theme_mod('saf_email',''); $phone=get_theme_mod('saf_phone',''); ?>
        <?php if($email): ?><small>E-mail</small><strong><?php echo esc_html($email); ?></strong><?php endif; ?>
        <?php if($phone): ?><small>Телефон</small><strong><?php echo esc_html($phone); ?></strong><?php endif; ?>
        <small>Компанія</small><strong>ТОВ «САФ ТРЕЙД УКРАЇНА»</strong>
      </div>
    </aside>
  </div>
</section>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>
