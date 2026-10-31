<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMXPath;

class WhatsAppCtaService
{
    public const DISPLAY_NUMBER = '085648983077';

    public function wordCount(): int
    {
        $ctaText = 'Butuh arah digital yang sesuai untuk bisnismu? Konsultasi WhatsApp '.self::DISPLAY_NUMBER;

        return count(preg_split('/\s+/u', trim($ctaText)) ?: []) * 5;
    }

    public function insertFive(string $html, ?string $articleTitle = null): string
    {
        if (! class_exists(DOMDocument::class)) {
            return $html;
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previousErrors = libxml_use_internal_errors(true);
        $dom->loadHTML('<!DOCTYPE html><html><head><meta charset="utf-8"></head><body><div id="ryo-article-content">'.$html.'</div></body></html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrors);

        $root = $dom->getElementById('ryo-article-content');
        if (! $root instanceof DOMElement) {
            return $html;
        }

        $xpath = new DOMXPath($dom);
        $previousCtas = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " ryo-whatsapp-cta ")]', $root);
        if ($previousCtas !== false) {
            foreach (iterator_to_array($previousCtas) as $previousCta) {
                $previousCta->parentNode?->removeChild($previousCta);
            }
        }

        $contentBlocks = [];
        $ctaAnchors = [];
        foreach ($root->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $contentBlocks[] = $child;
                if (in_array(strtolower($child->tagName), ['p', 'ul', 'ol', 'blockquote', 'table', 'figure'], true)) {
                    $ctaAnchors[] = $child;
                }
            }
        }
        if ($contentBlocks === []) {
            $paragraph = $dom->createElement('p');
            $paragraph->textContent = strip_tags($html);
            $root->appendChild($paragraph);
            $contentBlocks[] = $paragraph;
            $ctaAnchors[] = $paragraph;
        }
        if ($ctaAnchors === []) {
            $ctaAnchors = $contentBlocks;
        }

        $message = 'Halo Ryo Digital Website, saya ingin konsultasi terkait '.($articleTitle ?: 'kebutuhan website dan layanan digital').'.';
        $href = 'https://wa.me/6285648983077?text='.rawurlencode($message);

        for ($number = 1; $number <= 5; $number++) {
            $cta = $dom->createElement('aside');
            $cta->setAttribute('class', 'ryo-whatsapp-cta');
            $cta->setAttribute('aria-label', 'Konsultasi WhatsApp dengan Ryo Digital Website');

            $copy = $dom->createElement('p');
            $copy->setAttribute('class', 'ryo-whatsapp-cta-copy');
            $copy->textContent = 'Butuh arah digital yang sesuai untuk bisnismu?';
            $cta->appendChild($copy);

            $link = $dom->createElement('a');
            $link->setAttribute('href', $href);
            $link->setAttribute('target', '_blank');
            $link->setAttribute('rel', 'noopener noreferrer');
            $link->setAttribute('class', 'ryo-whatsapp-cta-link');
            $link->textContent = 'Konsultasi WhatsApp '.self::DISPLAY_NUMBER;
            $cta->appendChild($link);

            $blockIndex = min(count($ctaAnchors) - 1, (int) ceil($number * count($ctaAnchors) / 6) - 1);
            $block = $ctaAnchors[$blockIndex];
            if ($block->nextSibling) {
                $root->insertBefore($cta, $block->nextSibling);
            } else {
                $root->appendChild($cta);
            }
        }

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $dom->saveHTML($child);
        }

        return trim($output);
    }
}
