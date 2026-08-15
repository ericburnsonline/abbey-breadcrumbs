<?php

use MediaWiki\Title\Title;

class AbbeyBreadcrumbsHooks {

    /**
     * Add the namespace to the beginning of the breadcrumb generated
     * by the SubpageNavigation extension.
     *
     * Example:
     *
     * eBay > Situations > Cancellations > Not Shipped Yet
     */
    public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) {

        $title = $out->getTitle();

        // Do nothing for Special pages.
        if ( $title->isSpecialPage() ) {
            return;
        }

        // Main namespace has no namespace name to add.
        if ( $title->getNamespace() === NS_MAIN ) {
            return;
        }

        // Get the human-readable namespace name.
        // Examples: eBay, IoT, Projects, Reference
        $namespace = $title->getNsText();

        if ( $namespace === '' ) {
            return;
        }

        // Create a link to the namespace's Main page.
        //
        // Examples:
        // eBay:Main
        // IoT:Main
        // Projects:Main
        $namespaceMainTitle = Title::newFromText(
            $namespace . ':Main'
        );

        if ( !$namespaceMainTitle ) {
            return;
        }

        $linkRenderer = MediaWiki\MediaWikiServices::getInstance()
            ->getLinkRenderer();

        $namespaceLink = $linkRenderer->makeLink(
            $namespaceMainTitle,
            new HtmlArmor( htmlspecialchars( $namespace ) )
        );

        /*
         * SubpageNavigation places its breadcrumb in an OutputPage
         * indicator named "subpage-navigation".
         *
         * Generate its normal breadcrumb, then prepend the namespace.
         */
        if (
            class_exists( 'SubpageNavigation' ) &&
            SubpageNavigation::breadcrumbIsEnabled( $skin )
        ) {
            $breadcrumb = SubpageNavigation::breadCrumbNavigation( $title );

            if ( $breadcrumb !== false ) {

                $breadcrumb =
                    $namespaceLink .
                    ' <span class="abbey-breadcrumb-separator">›</span> ' .
                    $breadcrumb;

                $out->setIndicators( [
                    'subpage-navigation' => $breadcrumb
                ] );
            }
        }
    }
}
