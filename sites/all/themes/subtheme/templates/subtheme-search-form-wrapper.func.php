<?php
/**
 * @file
 * bootstrap-search-form-wrapper.func.php
 */

/**
 * Theme function implementation for bootstrap_search_form_wrapper.
 */
function subtheme_subtheme_search_form_wrapper($variables) {

  $output = '
  <ul class="nav navbar-nav navbar-right">
   <li class="expanded dropdown">
      <a href="#" title data-target="#" class="dropdown-toggle nolink" data-toggle="dropdown">
       <i title="glyphicon-search" class="icon glyphicon glyphicon-search" aria-hidden="true"></i>
       <span class="caret"></span>
     </a>
     <ul class="dropdown-menu" style="padding: 15px; min-width: 250px;">
       <li class="first leaf">
         <div class="row">
           <div class="col-md-12">';

  $output = '<div class="input-group">';
  $output .= $variables['element']['#children'];
  $output .= '<span class="input-group-btn">';
  $output .= '<button type="submit" class="btn btn-default">';
  // We can be sure that the font icons exist in CDN.
  if (theme_get_setting('bootstrap_cdn')) {
    $output .= _bootstrap_icon('search');
  }
  else {
    $output .= t('Search');
  }
  $output .= '</button>';
  $output .= '</span>';
  $output .= '</div>';

  $output .= '
            </div>
          </div>
        </li>
      </ul>
    </li>
  </ul>';

  return $output;
}
