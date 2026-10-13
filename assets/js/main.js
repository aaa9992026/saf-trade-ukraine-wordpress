document.addEventListener('DOMContentLoaded',()=>{
  const header=document.getElementById('site-header');
  const menuBtn=document.querySelector('.saf-menu-toggle');
  const menu=document.querySelector('.saf-menu');
  const closeMenu=()=>{if(menuBtn&&menu){menu.classList.remove('is-open');menuBtn.setAttribute('aria-expanded','false');document.body.classList.remove('saf-menu-open')}};
  if(menuBtn&&menu){menuBtn.addEventListener('click',()=>{const open=!menu.classList.contains('is-open');menu.classList.toggle('is-open',open);menuBtn.setAttribute('aria-expanded',String(open));document.body.classList.toggle('saf-menu-open',open)});menu.querySelectorAll('a').forEach(a=>a.addEventListener('click',closeMenu));}
  window.addEventListener('scroll',()=>header?.classList.toggle('is-scrolled',window.scrollY>18),{passive:true});


  // Resilient image fallback: if any remote or uploaded image fails, keep the layout intact.
  document.querySelectorAll('img').forEach(img=>{
    img.addEventListener('error',()=>{
      if(img.dataset.safFallbackApplied==='1')return;
      const fallback=img.dataset.safFallback||((window.SAFTrade&&SAFTrade.fallbackImage)||'');
      if(!fallback)return;
      img.dataset.safFallbackApplied='1';
      img.src=fallback;
    });
  });

  const modal=document.getElementById('saf-quote-modal');
  const modalInterest=()=>modal?.querySelector('input[name="interest"]');
  const openModal=(interest='')=>{if(!modal)return;modal.classList.add('is-open');modal.setAttribute('aria-hidden','false');document.body.classList.add('saf-modal-open');if(interest&&modalInterest())modalInterest().value=interest;setTimeout(()=>modal.querySelector('input[name="name"]')?.focus(),80)};
  const closeModal=()=>{if(!modal)return;modal.classList.remove('is-open');modal.setAttribute('aria-hidden','true');document.body.classList.remove('saf-modal-open')};
  document.querySelectorAll('[data-saf-quote]').forEach(btn=>btn.addEventListener('click',()=>openModal(btn.dataset.interest||'')));
  modal?.querySelectorAll('[data-saf-close]').forEach(el=>el.addEventListener('click',closeModal));
  document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeMenu();closeModal()}});

  document.querySelectorAll('.saf-ajax-form').forEach(form=>form.addEventListener('submit',async e=>{
    e.preventDefault();
    const status=form.querySelector('.saf-form-status');
    status.textContent='';status.className='saf-form-status';
    if(!form.reportValidity())return;
    form.classList.add('is-loading');
    try{
      const response=await fetch(SAFTrade.ajaxUrl,{method:'POST',body:new FormData(form),credentials:'same-origin'});
      if(!response.ok)throw new Error(SAFTrade.error);
      const text=await response.text();
      let data;
      try{data=JSON.parse(text)}catch(_){throw new Error(SAFTrade.error)}
      if(!data.success)throw new Error(data.data?.message||SAFTrade.error);
      status.textContent=data.data?.message||SAFTrade.success;status.classList.add('is-success');
      form.reset();
    }catch(err){status.textContent=err.message||SAFTrade.error;status.classList.add('is-error')}
    finally{form.classList.remove('is-loading')}
  }));

  const search=document.getElementById('saf-catalog-search');
  const filters=[...document.querySelectorAll('[data-filter]')];
  const cards=[...document.querySelectorAll('#saf-catalog-grid .saf-product-card')];
  const noResults=document.querySelector('.saf-no-results');
  let active='all';
  const applyFilters=()=>{
    const q=(search?.value||'').trim().toLowerCase();let shown=0;
    cards.forEach(card=>{const categoryOk=active==='all'||(card.dataset.categories||'').split(' ').includes(active);const searchOk=!q||(card.dataset.title||'').includes(q);const show=categoryOk&&searchOk;card.classList.toggle('is-hidden',!show);if(show)shown++});
    if(noResults)noResults.hidden=shown!==0;
  };
  search?.addEventListener('input',applyFilters);
  filters.forEach(btn=>btn.addEventListener('click',()=>{active=btn.dataset.filter||'all';filters.forEach(x=>x.classList.toggle('is-active',x===btn));applyFilters()}));

  if('IntersectionObserver' in window){const io=new IntersectionObserver(entries=>entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('is-visible');io.unobserve(e.target)}}),{threshold:.09});document.querySelectorAll('.saf-section,.saf-trust-strip,.saf-business,.saf-cta').forEach(el=>{el.classList.add('saf-reveal');io.observe(el)})}

  // WordPress.com/Jetpack can inject its comment UI asynchronously even when the theme itself
  // never calls comments_template(). On catalogue/company requests we remove that UI defensively.
  if(document.body.classList.contains('saf-comments-disabled')){
    const commentSelectors=[
      '#comments','#respond','.comments-area','.comment-respond','.comment-form','.comment-navigation',
      '.commentlist','.comment-list','.comments-title','.wp-block-comments','.wp-block-post-comments',
      '.wp-block-post-comments-form','.wp-block-comments-title','.wp-block-comment-template',
      '.jetpack-comments','.jetpack-comments-wrapper','.jetpack-comment-form','[class*="jetpack-comment"]','[id*="jetpack-comment"]',
      '.jp-carousel-comments-wrapper','#jp-carousel-comments','#jp-carousel-comment-form-container','#jp-carousel-comment-form',
      '[class*="jp-carousel-comment"]','[id*="jp-carousel-comment"]',
      'iframe[src*="comment" i]','iframe[id*="comment" i]','iframe[name*="comment" i]',
      'form[action*="wp-comments-post.php"]','[data-testid*="comment" i]'
    ];
    let purging=false;
    const purgeComments=()=>{
      if(purging)return; purging=true;
      try{document.querySelectorAll(commentSelectors.join(',')).forEach(el=>el.remove());}
      finally{purging=false;}
    };
    purgeComments();
    const commentObserver=new MutationObserver(()=>purgeComments());
    commentObserver.observe(document.body,{childList:true,subtree:true});
  }
});
