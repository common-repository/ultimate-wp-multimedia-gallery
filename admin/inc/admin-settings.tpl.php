<?php
if (!defined('ABSPATH')) exit;
$lightBoxType = ( get_option('lightBoxType') != '' ) ? get_option('lightBoxType') : 'facebook';
?>

<div id="wpmg_wrap">
	<?php include 'admin-header.tpl.php'; ?>
	<div class="CSSTableGenerator" id="tbl-wpmg">
        <h3>General Settings:</h3>
        <table class="wpmgInnerTbl" style="margin: 20px auto 0;"> 
            <tbody> 
                <tr>    
                    <td>LightBox Type</td>
                    <td class="">
                        <?php foreach (
                            [
                                'facebook' => 'Default', 'light_rounded' => 'Light Rounded', 'dark_rounded' => 'Dark Rounded',
                                'light_square' => 'Light Square', 'dark_square' => 'Dark Square', 
                            ] 
                            as $key => $type) : ?>
                            <div class="lighBoxTypeFieldGroup">
                                <input type="radio" name="lightBoxType" id="<?php echo $key ?>" value="<?php echo $key ?>" <?php if($lightBoxType == $key ) echo 'checked' ?>>
                                <label for="<?php echo $key ?>"><?php echo $type ?></label>
                            </div>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <tr>    
                    <td>YouTube Channel ID <?php if(!WPMG::$licenced) echo '<sup>Pro</sup>' ?></td>
                    <td class="wpmgGridFull"><input <?php if(!WPMG::$licenced) echo 'disabled' ?> type="text" name="youtube-chaneel-id" id="youtube-chaneel-id" value="<?php echo get_option('youtube-chaneel-id') ?>" placeholder="YouTube Chaneel ID"></td>
                </tr>
                <tr>    
                    <td>Social Meida HashTag <?php if(!WPMG::$licenced) echo '<sup>Pro</sup>' ?></td>
                    <td class="wpmgGridFull"><input <?php if(!WPMG::$licenced) echo 'disabled' ?> type="text" name="social-media-hastag" id="social-media-hastag" value="<?php echo get_option('social-media-hastag') ?>" placeholder="Social Meida HashTag"></td>
                </tr>
                <tr>
                    <td> </td>
                    <td style="text-align: right;"> 
                        <a href="#" class="button wpmg-update-settings">Update</a>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3>Filter Alignment</h3>
        <table class="wpmgInnerTbl" style="margin: 20px auto 0;"> 
            <tbody> 
                <tr>    
                    <td>Left Aligned Filters</td>
                    <td style="display: flex;justify-content: center;align-items: center;">
                        <input type="radio" id="left-aligned" name="filter-align" value="left-aligned" <?php if( get_option('wpmg-filter-align') == '' || get_option('wpmg-filter-align') == 'left-aligned' ) echo 'checked' ?>>
                        <label for="left-aligned">
                            <img src="<?php echo WPMG__URL ?>/admin/images/left-menu.png" alt="Left Alignment" width="" style="border: 1px solid #ccc; border-radius: 4px;">
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>Center Aligned Filters</td>
                    <td style="display: flex;justify-content: center;align-items: center;">
                        <input type="radio" id="center-aligned" name="filter-align" value="center-aligned" <?php if( get_option('wpmg-filter-align') == 'center-aligned' ) echo 'checked' ?>>
                        <label for="center-aligned">
                            <img src="<?php echo WPMG__URL ?>/admin/images/center-menu.png" alt="Center Alignment" width="" style="border: 1px solid #ccc; border-radius: 4px;">
                        </label>
                    </td>
                </tr>
               
            </tbody>
        </table>

        <h3>Filter Colors:</h3>
        <table class="wpmgInnerTbl" style="margin: 20px auto;"> 
            <tbody> 
                <tr>    
                    <td>Filter Wrapper BG</td>
                    <td class="wpmgGridFull"><input type="text" name="filter-wrapper-bg" id="filter-wrapper-bg" value="<?php echo get_option('filter-wrapper-bg') ?>"></td>
                </tr>
                <tr>    
                    <td>Button Text Color</td>
                    <td class="wpmgGridFull"><input type="text" name="filter-text-color" id="filter-text-color" value="<?php echo get_option('filter-text-color') ?>"></td>
                </tr>
                <tr>    
                    <td>Button BG Color</td>
                    <td class="wpmgGridFull"><input type="text" name="filter-bg-color" id="filter-bg-color" value="<?php echo get_option('filter-bg-color') ?>"></td>
                </tr>
                <tr>    
                    <td>Button Border Color</td>
                    <td class="wpmgGridFull"><input type="text" name="filter-border-color" id="filter-border-color" value="<?php echo get_option('filter-border-color') ?>"></td>
                </tr>

                <tr>    
                    <td>Active Button Text Color</td>
                    <td class="wpmgGridFull"><input type="text" name="act-filter-text-color" id="act-filter-text-color" value="<?php echo get_option('act-filter-text-color') ?>"></td>
                </tr>
                <tr>    
                    <td>Active Button BG Color</td>
                    <td class="wpmgGridFull"><input type="text" name="act-filter-bg-color" id="act-filter-bg-color" value="<?php echo get_option('act-filter-bg-color') ?>"></td>
                </tr>
                <tr>    
                    <td>Active Button Border Color</td>
                    <td class="wpmgGridFull"><input type="text" name="act-filter-border-color" id="act-filter-border-color" value="<?php echo get_option('act-filter-border-color') ?>"></td>
                </tr>
                <tr>
                    <td> </td>
                    <td style="text-align: right;"> 
                        <a href="#" class="button wpmg-update-filter-settings">Update</a>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3>Pagination Colors:</h3>
        <table class="wpmgInnerTbl" style="margin: 20px auto 0;"> 
            <tbody> 
                <tr>    
                    <td>Text Color</td>
                    <td class="wpmgGridFull"><input type="text" name="paginate-text-color" id="paginate-text-color" value="<?php echo get_option('paginate-text-color') ?>" placeholder="Pagination Text Color" disabled title="Pro Only"></td>
                </tr>
                <tr>    
                    <td>BG Color</td>
                    <td class="wpmgGridFull"><input type="text" name="paginate-bg-color" id="paginate-bg-color" value="<?php echo get_option('paginate-bg-color') ?>" placeholder="Pagination BG Color" disabled title="Pro Only"></td>
                </tr>
                <tr>    
                    <td>Active Text Color &nbsp; &nbsp;</td>
                    <td class="wpmgGridFull"><input type="text" name="act-paginate-text-color" id="act-paginate-text-color" value="<?php echo get_option('act-paginate-text-color') ?>" placeholder="Pagination Active Text Color" disabled title="Pro Only"></td>
                </tr>
                <tr>    
                    <td>Active BG Color</td>
                    <td class="wpmgGridFull"><input type="text" name="act-paginate-bg-color" id="act-paginate-bg-color" value="<?php echo get_option('act-paginate-bg-color') ?>" placeholder="Pagination Active BG Color" disabled title="Pro Only"></td>
                </tr>
                <tr>
                    <td> </td>
                    <td style="text-align: right;"> 
                        <a href="#" class="button wpmg-update-paginate-settings" disabled title="Pro Only">Update</a>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</div>