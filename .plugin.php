<?php
/**
 * Plugin Name: Classify
 * Plugin URI: https://github.com/idoalit/classify
 * Description: Class Recommendation (Internet access required)
 * Version: alpha-1.0.0
 * Author: Waris Agung Widodo
 * Author URI: https://github.com/idoalit
 */

use SLiMS\Plugins;

Plugins::hook(Plugins::BIBLIOGRAPHY_CUSTOM_FIELD_FORM, function($form, &$js) {
    $js .= <<<HTML
        function getRatting(persentase) {
            const nilaiBintang = persentase * 5;
            const rating = Math.round(nilaiBintang * 10) / 10;
            
            // Buat elemen SVG bintang emas
            const svgGoldStar = `
                <svg style="width:16px; height:16px; fill:gold" viewBox="0 0 24 24">
                <polygon points="12,2 15.09,9.26 22,9.92 17,15 18.18,22 12,18 5.82,22 7,15 2,9.92 8.91,9.26" />
                </svg>
            `;

            // Buat elemen SVG bintang abu-abu
            const svgGreyStar = `
                <svg style="width:16px; height:16px; fill:#ccc" viewBox="0 0 24 24">
                <polygon points="12,2 15.09,9.26 22,9.92 17,15 18.18,22 12,18 5.82,22 7,15 2,9.92 8.91,9.26" />
                </svg>
            `;

            const fullStars = Math.floor(rating);
            const halfStar = rating % 1 !== 0;

            // Gabungkan elemen SVG bintang emas, abu-abu, dan setengah bintang
            let ratingSVG = svgGoldStar.repeat(fullStars);
            if (halfStar) {
                ratingSVG += svgGreyStar;
            }
            ratingSVG += svgGreyStar.repeat(5 - Math.ceil(rating));

            return ratingSVG;
        }

        let clsWrapper = $('#simbioFormRowclass')
        clsWrapper.find('.alterCell2')
            .addClass('d-flex align-items-center w-100')
            .append('<div id="classify" class="d-flex align-items-center"><button id="getClass" class="btn btn-success ml-3">Get Recommendation</button><div id="setClass" class="font-weight-bold px-3"></div></div>')

        let classify = $('#getClass')
        classify.click((e) => {
            e.preventDefault()
            let titleVal = $('#title').val()
            if (titleVal === '') {
                $('#setClass').html('<span class="text-danger">Please, add title first!</span>')
                return
            }
            fetch('http://103.171.162.122:1111/classify/' + titleVal)
                .then(res => res.json())
                .then(res => {
                    $('#setClass').html('Class: <code>' + res.class + '</code> (' + getRatting(res.score) + ')')
                })
                .catch(err => {
                    $('#setClass').html('<span class="text-danger">Upss, service not available</span>')
                })
        })
    HTML;
});
