<?php declare(strict_types=1); ?>


  

  <div class="cookieBox" id="cookie-box">
    <p><?= h(tr('common.cookie_text', 'This website uses Cookies to enhance your experience.')) ?></p>
        <span id="cookie-accept"><?= h(tr('common.cookie_accept', 'Accept')) ?></span>
</div>
  <div id="container">
    


<?php require dirname(__DIR__, 2) . '/partials/roots-nav.php'; ?>
    <main id="main" class="">
        
            

<section class="homeHeader roots-hero scroll anima" id="home-header" data-anima-delay="4">
    
    <div class="homeHeader__mediaCards">
                                                                                                                                                            <div class="homeHeader__mediaCard">
                                  
  
          

  <video muted loop playsinline preload="auto" class="media vid">
    <source src="/media/video/hero/roots-agency-hero-01.mp4" type="video/mp4" />
  </video>


        
                </div>    
                                                                                                                <div class="homeHeader__mediaCard">
                                  
  
          

  <video muted loop playsinline preload="auto" class="media vid">
    <source src="/media/video/hero/roots-agency-hero-02.mp4" type="video/mp4" />
  </video>


        
                </div>    
                                                                                                                                                                                                            <div class="homeHeader__mediaCard">
                                  
  
          

  <video muted loop playsinline preload="auto" class="media vid">
    <source src="/media/video/hero/roots-agency-hero-03.mp4" type="video/mp4" />
  </video>


        
                </div>    
                                                                                                                                                                        <div class="homeHeader__mediaCard">
                                  
  
          

  <video muted loop playsinline preload="auto" class="media vid">
    <source src="/media/video/hero/roots-agency-hero-04.mp4" type="video/mp4" />
  </video>


        
                </div>    
                        </div>

    <div class="homeHeader__content">
        
              <h1 class="h0 homeHeader__heading zero h-anim anima" data-spanner="w" data-anima-delay=3><?= tr_html('home.hero.heading_html', '<p>Your  <span class=\'split\'> </span>  Business</p><p><i>N</i>ext <span class=\'split\'> </span> Level</p>') ?></h1>
  
        <p class="roots-hero__subcopy anima fade" data-anima-delay="18"><?= h(tr('home.hero.subcopy', 'Our agency prides itself on helping you take your business to the next level.')) ?></p>
        
    </div>
    
    <span class="homeHeader__decoText subheading anima fade" data-anima-delay="25"></span>
</section>                        

<section class="mediaSentence" id="company">
    <div class="mediaSentence__main pal scroll">
                <span class="subheading anima fade"></span>
        <div class="h0 mediaSentence__sentence pal-reveal h-reveal u-hideMobile" data-pal="0.155" data-pal-push="4.5"><?php roots_render_media_sentence_html('home.media.desktop_html'); ?></div>
        <div class="h0 mediaSentence__sentence anima scroll u-showMobile spanSkip h-anim anima" data-spanner="w"><?php roots_render_media_sentence_html('home.media.mobile_html'); ?></div>

  
    </div>
    <div class="mediaSentence__bottom scroll anima fade">
        <p class="body"><?= h(tr('home.media.bottom', 'From branding and social content to PR and integrated campaigns, our Yerevan team partners with hospitality, lifestyle, and growth-minded brands—building visibility, strengthening reputation, and turning creative strategy into measurable business growth.')) ?></p>
            
    </div>
</section>                        


<section class="workSlider " id="slider-cards" >
    <div class="workSlider__height workSlider__height--6">
        <div class="workSlider__stick" id="slider-stick">

            <div class="workSlider__top scroll anima fade"  >
                <span class="subheading"><?= h(tr('work.previous_work', 'Previous Work')) ?></span>
                <p class="body body--small"></p>
            </div>

            <div class="workSlider__slider">
                <div class="workSlider__rail scroll anima fade" id="slider-rail">
<?php foreach (array_slice(roots_work_cards(), 0, 5) as $card): ?>
<?php roots_render_work_card($card); ?>
<?php endforeach; ?>
<div class="workCard sliderCard workCard--cta workCard--seeMore">
                            <button type="button" class="workCard__media buttonHover roots-work-seeMore" aria-label="<?= h(tr('work.see_more_aria', 'See more previous work')) ?>">
                                <span class="arrowButton" >
                                    <span class="arrowButton__arrow"><img src="/ui/button_arrow.svg" /><img src="/ui/button_arrow.svg" /></span><span class="arrowButton__label" data-content="<?= h(tr('work.see_more', 'See More')) ?>"><span><?= h(tr('work.see_more', 'See More')) ?></span></span>
                                </span>
                            </button>
                        </div>
                                    </div>
            <div id="roots-work-pending" hidden aria-hidden="true">
<?php foreach (array_slice(roots_work_cards(), 5) as $card): ?>
<?php roots_render_work_card($card); ?>
<?php endforeach; ?>
            </div>
            </div>
        </div>
    </div>
</section>                        

<section class="companyData" id="services">

    <div class="companyData__bg pal scroll">
        <div class="companyData__bgInner">
                                        
    
    
    
                                                

    
      

      
      
      
                        
      
      
      <picture>
                                <source type="image/webp" srcset="/media/images/services/roots-agency-services-team.webp 1x, /media/images/services/roots-agency-services-team@2x.webp 2x" />
                <img
          src="/media/images/services/roots-agency-services-team.webp"
          class="media img lazy"
          alt="<?= h(tr('services.image_alt', 'Roots Agency team')) ?>"
          
        />
      </picture>
      
    
  

        
      
            <div class="companyData__bgOverlay anima pal-opacity" data-pal="0.5" data-pal-push="10" ></div>
        </div>
    </div>

    <div class="companyData__inner">
        <div class="pal u-hideMobile">
            <h2 class="h0 companyData__heading pal-reveal h-reveal" data-pal="0.25" data-pal-push="3.5"><?= tr_html('services.heading_html', '<p><i>F</i>ull service</p><p>marketing</p><p>company.</p>') ?></h2>
        </div>
              <h2 class="h0 companyData__heading anima scroll u-showMobile h-anim anima" data-spanner="w" ><?= tr_html('services.heading_html', '<p><i>F</i>ull service</p><p>marketing</p><p>company.</p>') ?></h2>
  
                    <div class="companyData__servicesList scroll">
                                <ol class="anima">
<?php foreach (roots_service_items() as $serviceKey): ?>
                                            <li><?= h(tr($serviceKey, '')) ?></li>   
<?php endforeach; ?>
                                    </ol>
                            <a href="#company" class="arrowButton anima fade" >
            <span class="arrowButton__arrow"><img src="/ui/button_arrow.svg" /><img src="/ui/button_arrow.svg" /></span><span class="arrowButton__label" data-content="<?= h(tr('services.about_roots', 'About Roots')) ?>"><span><?= h(tr('services.about_roots', 'About Roots')) ?></span></span>
        </a>    
    
            </div>
                <div class="companyData__bottom scroll anima fade">
            <p class="body"><?= h(tr('services.bottom', 'From strategy and social to branding, PR, audits, and master classes — our Yerevan team delivers full-service marketing for hospitality, lifestyle, and growth-minded brands.')) ?></p>
                        <a href="#company" class="arrowButton " >
            <span class="arrowButton__arrow"><img src="/ui/button_arrow.svg" /><img src="/ui/button_arrow.svg" /></span><span class="arrowButton__label" data-content="<?= h(tr('services.about_company', 'About Our Company')) ?>"><span><?= h(tr('services.about_company', 'About Our Company')) ?></span></span>
        </a>    
    
        </div>
    </div>

</section>                        

<section class="clientLogos" id="team">
    <div class="clientLogos__content">
        <div class="clientLogos__contentInner scroll">
            <p class="subheading anima text-fade"><?= h(tr('team.subheading', 'Our Team')) ?></p>
                              <h2 class="h0 clientLogos__heading h-anim anima" data-spanner="w" ><?= tr_html('team.heading_html', '<p>Meet</p><p>our</p><p><i>T</i><strong>EAM</strong></p>') ?></h2>
  
        </div>
    </div>
    <div class="clientLogos__logos scroll anima fade" id="logos">
<?php foreach (roots_team_members() as $member): ?>
<?php roots_render_team_card($member); ?>
<?php endforeach; ?>
            </div>
    <div class="logoBanner__row">
        <div class="logoBanner__rail" data-looper=".clientLogos" data-looper-speed="-1">
<?php foreach (roots_team_members() as $member): ?>
<?php roots_render_team_card($member, true); ?>
<?php endforeach; ?>
                    </div>
    </div>
</section>

<section class="shortHeader" id="contact">

    <div class="shortHeader__inner">
                              <h2 class="h0 shortHeader__heading zero h-anim" data-spanner="w" ><?= tr_html('contact.heading_html', '<p>Let&#8217;s  <span class="window"><picture><source type="image/webp" srcset="/media/images/contact/roots-agency-contact-hero.webp 1x, /media/images/contact/roots-agency-contact-hero@2x.webp 2x" /><img src="/media/images/contact/roots-agency-contact-hero.webp" class="media img " alt="Get in touch with Roots Agency" /></picture></span>  <i>c</i>hat</p>') ?></h2>
  
        <div class="shortHeader__bottom">
            <p class="body anima fade"><?= h(tr('contact.intro', 'Have a project in mind or want to learn more about our work? Fill out the form and we will get back to you as soon as possible.')) ?></p>
                    </div>
    </div>


</section>                        

<section class="contactForm">

    <div class="contactForm__inner scroll anima fade" data-anima-delay="5">
        


<form method="post" action="<?= h(localized_path('/')) ?>" accept-charset="UTF-8" id="contact-form" data-error-message="<?= h(tr('contact.form.error', 'Something went wrong, please try again.')) ?>">

    <input type="text" name="honey" id="honey" value="" tabindex="-1" autocomplete="off" aria-hidden="true">
    
    <div class="textField">
      <label class="textField__label" for="from-name"><?= h(tr('contact.form.name', 'Name')) ?><span>*</span></label>
      <input tabindex="2" class="textField__input getDirty" id="from-name" type="text" name="fromName" value="" placeholder="" required>
      
    </div>

    <div class="textField">
      <label class="textField__label" for="contact-company"><?= h(tr('contact.form.company', 'Company')) ?><span>*</span></label>
      <input tabindex="2" class="textField__input getDirty" id="contact-company" type="text" name="message[company]" value="" placeholder="">
      
    </div>

    <div class="textField">
        <label class="textField__label" for="from-email"><?= h(tr('contact.form.email', 'Email')) ?><span>*</span></label>
        <input tabindex="2" class="textField__input getDirty" id="from-email" type="email" name="fromEmail" value="" placeholder="" required>
        
    </div>

    <div class="textField">
      <label class="textField__label" for="message"><?= h(tr('contact.form.message', 'Message')) ?><span>*</span></label>
      <textarea tabindex="2" class="textField__input textField__textarea getDirty" rows="5"  id="message" name="message[message]" placeholder="" required></textarea>
      
    </div>


    <span class="ctaButton ctaButton--dark submitButton" >
        <input class="ctaButton__label" type="submit" value="<?= h(tr('contact.form.submit', 'Send Message')) ?>">
    </span>


    <p class="body body--large contactForm__success"><?= h(tr('contact.form.success', 'Thank you for your message.')) ?></p>
    

  </form>
        
    </div>

</section>                        

<div class="roots-page-end">
<section class="officeList">
    <h2 class="subheading anima fade scroll"><?= h(tr('office.studio', 'Our Studio')) ?></h2>
    <div class="officeList__list">
                            <div class="officeList__office scroll anima fade" data-timezone="4">
                      <h3 class="h0 officeList__name h-anim anima" data-spanner="w" ><p><?= h(tr('office.name', 'Yerevan, AM')) ?></p></h3>
  
                <p class="officeList__address"><?= nl2br(h(tr('office.address', "Vazgen Sargsyan Street 26/3\nYerevan, Armenia"))) ?></p>
                <a href="https://www.google.com/maps/search/Vazgen+Sargsyan+26%2F3+Yerevan" target="_blank" rel="noopener noreferrer"></a>
            </div>
                <div class="officeList__timeWrap">
            <div data-follow=".officeList__list" data-follow-jump data-follow-diag>
                <div class="officeList__time"><img src="/ui/time_icon.svg" alt="" /><span id="time">--:--</span></div>
            </div>
        </div>
    </div>
    

</section>            
            
 
<footer class="footer roots-footer scroll">
    <img class="footer__bg" src="/ui/footer_bg_light.svg"/>

    <div class="roots-footer-body">
    <div class="footer__mainLinks">
                  
          <?php roots_render_flip_link((string) ($site['social']['instagram'] ?? ''), 'nav.instagram', 'Instagram', 'link footer__mainLink anima maskIn', true); ?>

          <?php roots_render_flip_link((string) ($site['social']['facebook'] ?? ''), 'nav.facebook', 'Facebook', 'link footer__mainLink anima maskIn', true); ?>

          <?php roots_render_flip_link((string) ($site['social']['linkedin'] ?? ''), 'nav.linkedin', 'LinkedIn', 'link footer__mainLink anima maskIn', true); ?>

  
    </div>

    <aside class="roots-footer-instagram anima fade scroll" aria-label="<?= h(tr('footer.instagram_aria', 'Roots Agency on Instagram')) ?>">
        <a href="<?= h((string) ($site['social']['instagram'] ?? '')) ?>" class="roots-iphone-frame" target="_blank" rel="noopener noreferrer">
            <div class="roots-iphone-frame__bezel">
                <div class="roots-iphone-frame__screen">
                    <img
                        src="/media/images/footer/roots-agency-footer-instagram.webp"
                        class="media img"
                        alt="<?= h(tr('footer.instagram_alt', 'Roots Agency Instagram profile')) ?>"
                        width="591"
                        height="1280"
                        loading="lazy"
                    />
                </div>
            </div>
        </a>
    </aside>
    </div>

    <div class="footer__bottom anima fade scroll" data-anima-delay="10">
                <span class="roots-footer-credit">Web design &amp; development by <a href="https://www.payam-dehkordy.com/" target="_blank" rel="noopener noreferrer">Payam&nbsp;Dehkordy</a></span>
        </div>
    
</footer>
</div>
      
      <div id="htmlclass" data-class=""></div>
    </main>

  </div>

  <div class="transition-fade"></div>

  <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js"></script>
  <script type="text/javascript" src="<?= h(asset('/features/roots-site-chrome.js')) ?>"></script>
  <script type="text/javascript" src="/features/roots-hero-video.js"></script>
  <script type="text/javascript" src="/dist/scripts.min.js?v=7c85f98c63f5e1f89737e800920875f74ad6abf9"></script>
  <script type="text/javascript" src="/features/roots-contact-header.js"></script>
  <script type="text/javascript" src="/features/roots-contact-form.js"></script>
  <script type="text/javascript" src="/features/roots-scroll-top.js"></script>
  <script type="text/javascript" src="/features/roots-nav-scroll.js"></script>
  <script type="text/javascript" src="/features/roots-work-slider.js"></script>

  <!-- BrowserSync Hook -->

