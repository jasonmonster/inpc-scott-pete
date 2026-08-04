<?php
/**
 * Scott Pete - Contact Page Template (child override of ipc-base)
 *
 * Template Name: Contact
 *
 * Indiana-Kitchen-style layout: a page heading, then a two-column row with a
 * square framed featured image on the left and the contact details (the page
 * content) on the right.
 *
 * Set the square image via the page's Featured Image; edit the address, phone,
 * and email in the page content.
 *
 * @package ScottPete
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

while ( have_posts() ) : the_post(); ?>
<section class="contact-page">
    <div class="site-wrapper">

        <h1 class="contact-title">Contact <?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>

        <div class="contact-layout">
            <?php if ( has_post_thumbnail() ) : ?>
                <div class="contact-photo">
                    <?php the_post_thumbnail( 'large', array( 'class' => 'contact-photo-img' ) ); ?>
                </div>
            <?php endif; ?>

            <div class="contact-details">
                <?php the_content(); ?>
            </div>
        </div>

    </div>
</section>
<?php endwhile;

get_footer();
