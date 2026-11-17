<?php
$isPro = framePts::_()->getModule('supsystic_promo')->isPro();
$upsell = framePts::_()->getModule('upsell');
$isHoriz = isset($this->table['params']['is_horisontal_row_type']['val']) && $this->table['params']['is_horisontal_row_type']['val'] == 1;
/*
 * Settings of the table editor in the look of the Data Tables and Photo Gallery editors: group tabs with a search,
 * and the settings of each group in cards (admin.tables.ui.css / admin.tables.ui.js). Only the layout is new: every
 * field keeps its name, value, id and classes, so admin.tables.edit.js, the undo buffer (it copies .ptsSettingsContent)
 * and PRO work with it as before. The tab panes are the direct div children of .ptsSettingsContent (main, design,
 * toggle) - the tab script toggles "active" on divs inside it, so the cards are <section> elements.
 */
?>
<section>
	<div class="supsystic-item supsystic-panel" style="padding-left: 10px;">
		<div id="containerWrapper">
			<div class="ptsTableSettingsShell row pts-ed" data-pts-ed>
                <h3 class="ptsSettingsTabs nav-tab-wrapper pts-ed-nav">
                    <span class="pts-ed-groups">
                        <a class="nav-tab nav-tab-active" href="#main" data-href="main"><i class="fa fa-fw fa-wrench"></i> Main</a>
                        <a class="nav-tab" href="#design" data-href="design"><i class="fa fa-fw fa-eye"></i> Design</a>
                        <a class="nav-tab" href="#toggle" data-href="toggle"><i class="fa fa-fw fa-toggle-on"></i> Toggle<?php if (!$isPro) { ?> <span class="pts-ed-pro-dot">PRO</span><?php } ?></a>
                    </span>

                    <span class="undoButtons" style="display:none;">
                        <button id="ptsUndoButton" class="button button-sup-small" disabled title="Undo"><i class="fa fa-undo" aria-hidden="true"></i> Undo</button>
                        <button id="ptsRedoButton" class="button button-sup-small" disabled title="Redo"><i class="fa fa-repeat" aria-hidden="true"></i> Redo</button>
                        <span id="ptsUndoProcess" style="display:none;"><i class="fa fa-spinner fa-spin"></i> Processing...</span>
                    </span>
                    <label class="pts-ed-search">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <input type="search" data-pts-ed-search placeholder="<?php echo esc_attr__('Search settings', PTS_LANG_CODE); ?>" aria-label="<?php echo esc_attr__('Search settings', PTS_LANG_CODE); ?>" autocomplete="off">
                    </label>
                </h3>
                <p class="pts-ed-empty" data-pts-ed-empty hidden><?php esc_html_e('No settings match your search.', PTS_LANG_CODE); ?></p>
                <div class="ptsSettingsContent">
                    <div id="#main" class="active main">
                        <?php echo htmlPts::hidden('params[is_horisontal_row_type]', [
                          'value' => isset($this->table['params']['is_horisontal_row_type']) ? $this->table['params']['is_horisontal_row_type']['val'] : '0',
                        ]); ?>
                        <section class="pts-ed-card">
                            <header class="pts-ed-card-head">
                                <span class="pts-ed-card-icon"><i class="fa fa-th" aria-hidden="true"></i></span>
                                <span class="pts-ed-card-title"><b><?php $isHoriz ? _e('Rows and columns', PTS_LANG_CODE) : _e('Columns and rows', PTS_LANG_CODE); ?></b><small><?php _e('Plans and features of the table.', PTS_LANG_CODE); ?></small></span>
                            </header>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <span class="pts-ed-label">
                                    <?php if ($isHoriz) { ?>
                                        <span class="sup-complete-txt"><?php _e('Rows', PTS_LANG_CODE); ?></span>
                                        <span class="sup-reduce-txt"><?php _e('Rows', PTS_LANG_CODE); ?></span>
                                    <?php } else { ?>
                                        <span class="sup-complete-txt"><?php _e('Columns', PTS_LANG_CODE); ?></span>
                                        <span class="sup-reduce-txt"><?php _e('Cols', PTS_LANG_CODE); ?></span>
                                    <?php } ?>
                                    <?php if ($isHoriz) { ?>
                                        <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('New row will be added in the end of your table rows area.', PTS_LANG_CODE)); ?>"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('New column will be added in the end of your table.', PTS_LANG_CODE)); ?>"></i>
                                    <?php } ?>
                                </span>
                                <span class="pts-ed-ctl">
                                    <span class="pts-ed-count ptsTableColsNum ptsTableColsNum_<?php echo $this->table['view_id']; ?>"><?php echo isset($this->table['params']['cols_num']) ? $this->table['params']['cols_num']['val'] : 0; ?></span>
                                    <a href="#" class="button button-sup-small ptsAddColumnBtn">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                        <?php if ($isHoriz) { ?>
                                            <span class="sup-complete-txt"><?php _e('Add Row', PTS_LANG_CODE); ?></span>
                                            <span class="sup-reduce-txt"><?php _e('Add Row', PTS_LANG_CODE); ?></span>
                                        <?php } else { ?>
                                            <span class="sup-complete-txt"><?php _e('Add Column', PTS_LANG_CODE); ?></span>
                                            <span class="sup-reduce-txt"><?php _e('Add Col', PTS_LANG_CODE); ?></span>
                                        <?php } ?>
                                    </a>
                                </span>
                            </div>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <span class="pts-ed-label">
                                    <?php $isHoriz ? _e('Cols', PTS_LANG_CODE) : _e('Rows', PTS_LANG_CODE); ?>
                                    <?php if ($isHoriz) { ?>
                                        <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('New column will be added in the end of your table.', PTS_LANG_CODE)); ?>"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('New row will be added in the end of your table rows area.', PTS_LANG_CODE)); ?>"></i>
                                    <?php } ?>
                                </span>
                                <span class="pts-ed-ctl">
                                    <span class="pts-ed-count ptsTableRowsNum ptsTableRowsNum_<?php echo $this->table['view_id']; ?>"><?php echo isset($this->table['params']['rows_num']) ? $this->table['params']['rows_num']['val'] : 0; ?></span>
                                    <a href="#" class="button button-sup-small ptsAddRowBtn"><i class="fa fa-plus" aria-hidden="true"></i> <?php $isHoriz ? _e('Add Col', PTS_LANG_CODE) : _e('Add Row', PTS_LANG_CODE); ?></a>
                                </span>
                            </div>
                        </section>
                        <section class="pts-ed-card">
                            <header class="pts-ed-card-head">
                                <span class="pts-ed-card-icon"><i class="fa fa-arrows-h" aria-hidden="true"></i></span>
                                <span class="pts-ed-card-title"><b><?php _e('Width', PTS_LANG_CODE); ?></b><small><?php _e('Set the width of the whole table or of each column.', PTS_LANG_CODE); ?></small></span>
                            </header>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <span class="pts-ed-label">
                                    <label class="pts-ed-choice">
                                        <?php echo htmlPts::radiobutton('params[calc_width]', [
                                          'value' => 'table',
                                          'checked' => isset($this->table['params']['calc_width']) && $this->table['params']['calc_width']['val'] == 'table',
                                        ]); ?>
                                        <span class="sup-complete-txt"><?php _e('Table Width', PTS_LANG_CODE); ?></span>
                                        <span class="sup-reduce-txt"><?php _e('Tbl. Width', PTS_LANG_CODE); ?></span>
                                    </label>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Set width for table. Width for each column in this case will be calculated as width of whole table divided for total columns number.', PTS_LANG_CODE)); ?>"></i>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::text('params[table_width]', [
                                      'value' => isset($this->table['params']['table_width']) ? $this->table['params']['table_width']['val'] : 0,
                                      'attrs' => 'style="width: 50px"',
                                    ]); ?>
                                    <span class="pts-ed-seg">
                                        <label>
                                            <?php echo htmlPts::radiobutton('params[table_width_measure]', [
                                              'value' => 'px',
                                              'checked' => isset($this->table['params']['table_width_measure']) && $this->table['params']['table_width_measure']['val'] == 'px',
                                            ]); ?><span>px</span>
                                        </label>
                                        <label>
                                            <?php echo htmlPts::radiobutton('params[table_width_measure]', [
                                              'value' => '%',
                                              'checked' => isset($this->table['params']['table_width_measure']) && $this->table['params']['table_width_measure']['val'] == '%',
                                            ]); ?><span>%</span>
                                        </label>
                                    </span>
                                </span>
                            </div>
                            <?php if (empty($this->table['params']['is_horisontal_row_type']['val'])) { ?>
                                <div class="ptsTableSetting supMd3 pts-ed-row">
                                    <span class="pts-ed-label">
                                        <label class="pts-ed-choice">
                                            <?php echo htmlPts::radiobutton('params[calc_width]', [
                                              'value' => 'col',
                                              'checked' => !isset($this->table['params']['calc_width']) || $this->table['params']['calc_width']['val'] == 'col',
                                            ]); ?>
                                            <?php _e('Column Width', PTS_LANG_CODE); ?>
                                        </label>
                                        <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Set width for each column. Total table width in this case will be calculated as sum of all your columns width.', PTS_LANG_CODE)); ?>"></i>
                                    </span>
                                    <span class="pts-ed-ctl">
                                        <?php echo htmlPts::text('params[col_width]', [
                                          'value' => isset($this->table['params']['col_width']) ? $this->table['params']['col_width']['val'] : 186, //186 - normal, default col width
                                          'attrs' => 'style="width: 50px"',
                                        ]); ?>
                                        <span class="pts-ed-unit">px</span>
                                    </span>
                                </div>
                            <?php } ?>
                        </section>
                        <section class="pts-ed-card">
                            <header class="pts-ed-card-head">
                                <span class="pts-ed-card-icon"><i class="fa fa-sliders" aria-hidden="true"></i></span>
                                <span class="pts-ed-card-title"><b><?php _e('Layout and loading', PTS_LANG_CODE); ?></b><small><?php _e('Text alignment, phones and tablets, loading on the page.', PTS_LANG_CODE); ?></small></span>
                            </header>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <span class="pts-ed-label">
                                    <?php _e('Text Align', PTS_LANG_CODE); ?>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Text align in table: left, center, right', PTS_LANG_CODE)); ?>"></i>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::selectbox('params[text_align]', [
                                      'options' => ['left' => 'left', 'center' => 'center', 'right' => 'right'],
                                      'value' => isset($this->table['params']['text_align']['val']) ? $this->table['params']['text_align']['val'] : 'center',
                                      'attrs' => 'class="chosen"',
                                    ]); ?>
                                </span>
                            </div>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <!-- Here we used Enable as option even for hide param - to make it more user-friendly - Like It:) -->
                                <span class="pts-ed-label">
                                    <label for="ptsEdEnbResponsive">
                                        <span class="sup-complete-txt"><?php _e('Enable Responsivity', PTS_LANG_CODE); ?></span>
                                        <span class="sup-reduce-txt"><?php _e('Enb. Responsivity', PTS_LANG_CODE); ?></span>
                                    </label>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(
                                      __(
                                        'When device screen is small (mobile device or some tablets) usually table will go into responsive mode: all columns will be shown one-by-one below each other. But if you need to disable this feature - you can do this with this option. This feature influences only on frontend table view.',
                                        PTS_LANG_CODE,
                                      ),
                                    ); ?>"></i>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::checkbox('params[enb_responsive]', [
                                      'checked' => !(isset($this->table['params']['dsbl_responsive']) ? (int) $this->table['params']['dsbl_responsive']['val'] : 0),
                                      'attrs' => 'id="ptsEdEnbResponsive"',
                                    ]); ?>
                                </span>
                            </div>
                            <?php if (empty($this->table['params']['is_horisontal_row_type']['val'])) { ?>
                                <div class="ptsTableSetting supMd3 ptsRespMinColW ptsDisplNone pts-ed-row">
                                    <span class="pts-ed-label">
                                        <?php _e('Min Column Width', PTS_LANG_CODE); ?>
                                        <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Column width (is indicated in pixels by default) at which table will go to responsive mode', PTS_LANG_CODE)); ?>"></i>
                                    </span>
                                    <span class="pts-ed-ctl">
                                        <?php echo htmlPts::text('params[resp_min_col_width]', [
                                          'value' => isset($this->table['params']['resp_min_col_width']) ? $this->table['params']['resp_min_col_width']['val'] : 150,
                                          'attrs' => 'style="width: 50px"',
                                        ]); ?>
                                        <span class="pts-ed-unit"><?php _e('px', PTS_LANG_CODE); ?></span>
                                    </span>
                                </div>
                            <?php } ?>
                            <div class="ptsTableSetting supMd3 pts-ed-row<?php echo $isPro ? '' : ' pts-ed-locked'; ?>">
                                <span class="pts-ed-label">
                                    <?php _e('AJAX Loading', PTS_LANG_CODE); ?>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(
                                      __('Suitable if you need to load the table by action, but not when the page is initiated.<br> "toggle by header" - you can also use " title=\'custom label\' " param in shortcode to enter custom label.', PTS_LANG_CODE),
                                    ); ?>"></i>
                                    <?php echo $upsell ? $upsell->getProTag('ajax') : ''; ?>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::selectbox('params[ajax]', [
                                      'options' => ['disable' => 'disable', 'toggle_by_header' => 'toggle by header'],
                                      'value' => isset($this->table['params']['ajax']['val']) ? $this->table['params']['ajax']['val'] : 'disable',
                                      'attrs' => $isPro ? 'class="chosen"' : 'disabled class="chosen"',
                                    ]); ?>
                                </span>
                            </div>
                            <?php if (!$isPro) { ?>
                                <?php if ($upsell) { ?>
                                    <?php echo $upsell->getProNote('ajax', __('Loading the table on a click of its title is in Pricing Table PRO and in Data Tables Pro.', PTS_LANG_CODE)); ?>
                                <?php } else { ?>
                                    <div style="margin-top: 7px;">
                                        <a target="_blank" href="https://supsystic.com/plugins/pricing-table/" class="button">Get PRO</a>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </section>
                        <?php if (!$isPro && $upsell) { ?>
                            <section class="pts-ed-card pts-up-card">
                                <div class="pts-up-card-head">
                                    <span class="pts-up-card-eyebrow"><i class="fa fa-diamond" aria-hidden="true"></i> PRO</span>
                                    <h3><?php _e('Do more with this table', PTS_LANG_CODE); ?></h3>
                                    <p><?php _e('Your settings stay as they are. These tools are in Pricing Table PRO and in Data Tables Pro.', PTS_LANG_CODE); ?></p>
                                </div>
                                <div class="pts-up-card-grid">
                                    <button type="button" data-pts-upsell="toggle"><i class="fa fa-toggle-on" aria-hidden="true"></i><?php _e('Monthly / yearly switch', PTS_LANG_CODE); ?></button>
                                    <button type="button" data-pts-upsell="schedule"><i class="fa fa-calendar" aria-hidden="true"></i><?php _e('Scheduled columns', PTS_LANG_CODE); ?></button>
                                    <button type="button" data-pts-upsell="ajax"><i class="fa fa-bolt" aria-hidden="true"></i><?php _e('Load on click', PTS_LANG_CODE); ?></button>
                                    <button type="button" data-pts-upsell="templates"><i class="fa fa-th-large" aria-hidden="true"></i><?php _e('37 PRO templates', PTS_LANG_CODE); ?></button>
                                </div>
                                <div class="pts-up-card-foot"><?php echo $upsell->getDtButton(); ?></div>
                            </section>
                        <?php } ?>
                    </div>
                    <div id="#design" class="design">
                        <section class="pts-ed-card">
                            <header class="pts-ed-card-head">
                                <span class="pts-ed-card-icon"><i class="fa fa-paint-brush" aria-hidden="true"></i></span>
                                <span class="pts-ed-card-title"><b><?php _e('Font and colors', PTS_LANG_CODE); ?></b><small><?php _e('Common font and colors; any text can still get its own in the table.', PTS_LANG_CODE); ?></small></span>
                            </header>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <span class="pts-ed-label">
                                    <?php _e('Font', PTS_LANG_CODE); ?>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Font for table. You can always set other font for any text element using text editor tool. Just click on text - and start edit it!', PTS_LANG_CODE)); ?>"></i>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::fontsList('params[font_family]', [
                                      'value' => isset($this->table['params']['font_family']) ? $this->table['params']['font_family']['val'] : '',
                                      'attrs' => 'class="chosen"',
                                    ]); ?>
                                </span>
                            </div>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <span class="pts-ed-label">
                                    <?php _e('Background Color', PTS_LANG_CODE); ?>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Common background color for table.', PTS_LANG_CODE)); ?>"></i>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::hidden('params[bg_color]', [
                                      'attrs' => 'class="ptsColorPickBgColor"',
                                      'value' => isset($this->table['params']['bg_color']) ? $this->table['params']['bg_color']['val'] : '#fff',
                                    ]); ?>
                                    <div class="ptsTear ptsColorPickBgColorTear"></div>
                                </span>
                            </div>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <span class="pts-ed-label">
                                    <?php _e('Header Text Color', PTS_LANG_CODE); ?>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Text color for table columns header element. You can always specify text color for any text element inside table using text editor.', PTS_LANG_CODE)); ?>"></i>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::hidden('params[text_color_header]', [
                                      'attrs' => 'class="ptsColorPickTextColorHeader"',
                                      'value' => isset($this->table['params']['text_color_header']) ? $this->table['params']['text_color_header']['val'] : '',
                                    ]); ?>
                                    <div class="ptsTear ptsColorPickTextColorHeaderTear"></div>
                                </span>
                            </div>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <span class="pts-ed-label">
                                    <span class="sup-complete-txt"><?php _e('Description Text Color', PTS_LANG_CODE); ?></span>
                                    <span class="sup-reduce-txt"><?php _e('Desc. Text Color', PTS_LANG_CODE); ?></span>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Text color for table columns description element. You can always specify text color for any text element inside table using text editor.', PTS_LANG_CODE)); ?>"></i>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::hidden('params[text_color_desc]', [
                                      'attrs' => 'class="ptsColorPickTextColorDesc"',
                                      'value' => isset($this->table['params']['text_color_desc']) ? $this->table['params']['text_color_desc']['val'] : '',
                                    ]); ?>
                                    <div class="ptsTear ptsColorPickTextColorDescTear"></div>
                                </span>
                            </div>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <span class="pts-ed-label">
                                    <?php _e('Rows Text Color', PTS_LANG_CODE); ?>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Common text color for table. You can always specify text color for any text element inside table using text editor.', PTS_LANG_CODE)); ?>"></i>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::hidden('params[text_color]', [
                                      'attrs' => 'class="ptsColorPickTextColor"',
                                      'value' => isset($this->table['params']['text_color']) ? $this->table['params']['text_color']['val'] : '',
                                    ]); ?>
                                    <div class="ptsTear ptsColorPickTextColorTear"></div>
                                </span>
                            </div>
                        </section>
                        <section class="pts-ed-card">
                            <header class="pts-ed-card-head">
                                <span class="pts-ed-card-icon"><i class="fa fa-columns" aria-hidden="true"></i></span>
                                <span class="pts-ed-card-title"><b><?php _e('Parts of the table', PTS_LANG_CODE); ?></b><small><?php _e('Show or hide the head, footer and description parts.', PTS_LANG_CODE); ?></small></span>
                            </header>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <span class="pts-ed-label">
                                    <label for="ptsEdEnbDescCol">
                                        <span class="sup-complete-txt"><?php $isHoriz ? _e('Enable Description Row', PTS_LANG_CODE) : _e('Enable Description Column', PTS_LANG_CODE); ?></span>
                                        <span class="sup-reduce-txt"><?php $isHoriz ? _e('Enb. Desc. Row', PTS_LANG_CODE) : _e('Enb. Desc. Col', PTS_LANG_CODE); ?></span>
                                    </label>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Add additional description column into table. You can add there descriptions for your rows.', PTS_LANG_CODE)); ?>"></i>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::checkbox('params[enb_desc_col]', [
                                      'checked' => isset($this->table['params']['enb_desc_col']) ? (int) $this->table['params']['enb_desc_col']['val'] : 0,
                                      'attrs' => 'id="ptsEdEnbDescCol"',
                                    ]); ?>
                                </span>
                            </div>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <!-- Here we used Enable as option even for hide param - to make it more user-friendly - Like It:) -->
                                <span class="pts-ed-label">
                                    <label for="ptsEdEnbHeadRow">
                                        <span class="sup-complete-txt"><?php $isHoriz ? _e('Enable Head Column', PTS_LANG_CODE) : _e('Enable Head Row', PTS_LANG_CODE); ?></span>
                                        <span class="sup-reduce-txt"><?php $isHoriz ? _e('Enb. Head Column', PTS_LANG_CODE) : _e('Enb. Head Row', PTS_LANG_CODE); ?></span>
                                    </label>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('By unchecking the box you hide head row in all columns. Usually it is the first row in table.', PTS_LANG_CODE)); ?>"></i>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::checkbox('params[enb_head_row]', [
                                      'checked' => !(isset($this->table['params']['hide_head_row']) && (int) $this->table['params']['hide_head_row']['val']),
                                      'attrs' => 'id="ptsEdEnbHeadRow"',
                                    ]); ?>
                                </span>
                            </div>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <!-- Here we used Enable as option even for hide param - to make it more user-friendly - Like It:) -->
                                <span class="pts-ed-label">
                                    <label for="ptsEdEnbFootRow">
                                        <span class="sup-complete-txt"><?php $isHoriz ? _e('Enable Footer Column', PTS_LANG_CODE) : _e('Enable Footer Row', PTS_LANG_CODE); ?></span>
                                        <span class="sup-reduce-txt"><?php $isHoriz ? _e('Enb. Footer Column', PTS_LANG_CODE) : _e('Enb. Footer Row', PTS_LANG_CODE); ?></span>
                                    </label>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('By ubchecking the box you hide footer row in all columns. Usually it is last row in table.', PTS_LANG_CODE)); ?>"></i>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::checkbox('params[enb_foot_row]', [
                                      'checked' => !(isset($this->table['params']['hide_foot_row']) && (int) $this->table['params']['hide_foot_row']['val']),
                                      'attrs' => 'id="ptsEdEnbFootRow"',
                                    ]); ?>
                                </span>
                            </div>
                            <?php if (!isset($this->table['params']['is_horisontal_row_type']['val']) || $this->table['params']['is_horisontal_row_type']['val'] != 1) { ?>
                                <div class="ptsTableSetting supMd3 pts-ed-row">
                                    <!-- Here we used Enable as option even for hide param - to make it more user-friendly - Like It:) -->
                                    <span class="pts-ed-label">
                                        <label for="ptsEdEnbDescRow">
                                            <span class="sup-complete-txt"><?php _e('Enable Description Row', PTS_LANG_CODE); ?></span>
                                            <span class="sup-reduce-txt"><?php _e('Enb. Description Row', PTS_LANG_CODE); ?></span>
                                        </label>
                                        <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('By ubchecking the box you hide description row in all columns.', PTS_LANG_CODE)); ?>"></i>
                                    </span>
                                    <span class="pts-ed-ctl">
                                        <?php echo htmlPts::checkbox('params[enb_desc_row]', [
                                          'checked' => !(isset($this->table['params']['hide_desc_row']) && (int) $this->table['params']['hide_desc_row']['val']),
                                          'attrs' => 'id="ptsEdEnbDescRow"',
                                        ]); ?>
                                    </span>
                                </div>
                            <?php } ?>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <span class="pts-ed-label">
                                    <span class="sup-complete-txt"><?php _e('Vertical Padding', PTS_LANG_CODE); ?></span>
                                    <span class="sup-reduce-txt"><?php _e('Vert. Padding', PTS_LANG_CODE); ?></span>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Vertical padding for column.', PTS_LANG_CODE)); ?>"></i>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::text('params[vert_padding]', [
                                      'value' => isset($this->table['params']['vert_padding']) ? $this->table['params']['vert_padding']['val'] : 0,
                                      'attrs' => 'style="width: 50px"',
                                    ]); ?>
                                    <span class="pts-ed-unit"><?php _e('px', PTS_LANG_CODE); ?></span>
                                </span>
                            </div>
                        </section>
                        <section class="pts-ed-card">
                            <header class="pts-ed-card-head">
                                <span class="pts-ed-card-icon"><i class="fa fa-magic" aria-hidden="true"></i></span>
                                <span class="pts-ed-card-title"><b><?php _e('Placement and effects', PTS_LANG_CODE); ?></b><small><?php _e('Where the table stands on the page and how it reacts.', PTS_LANG_CODE); ?></small></span>
                            </header>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <span class="pts-ed-label">
                                    <?php _e('Table Align', PTS_LANG_CODE); ?>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Table align in page: left, center, right, none', PTS_LANG_CODE)); ?>"></i>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::selectbox('params[table_align]', [
                                      'options' => ['left' => 'left', 'center' => 'center', 'right' => 'right', 'none' => 'none'],
                                      'value' => isset($this->table['params']['table_align']['val']) ? $this->table['params']['table_align']['val'] : 'none',
                                      'attrs' => 'class="chosen"',
                                    ]); ?>
                                </span>
                            </div>
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <span class="pts-ed-label">
                                    <label for="ptsEdEnbHoverAnimation"><?php _e('Enable Hover Animation', PTS_LANG_CODE); ?></label>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Animate column when mouse is hovering on it. Will work ONLY on frontend, disabled in admin area WySiWyg editor as it can break it in edit mode.', PTS_LANG_CODE)); ?>"></i>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::checkbox('params[enb_hover_animation]', [
                                      'checked' => isset($this->table['params']['enb_hover_animation']) ? (int) $this->table['params']['enb_hover_animation']['val'] : 0,
                                      'attrs' => 'id="ptsEdEnbHoverAnimation"',
                                    ]); ?>
                                </span>
                            </div>
                            <!-- Responsive text option
                            <div class="ptsTableSetting supMd3">
                                <label>
                                    <?php echo htmlPts::checkbox('params[responsive_text]', [
                                      'checked' => isset($this->table['params']['responsive_text']) ? (int) $this->table['params']['responsive_text']['val'] : 0,
                                    ]); ?>
                                    <span class="sup-complete-txt"><?php _e('Enable Responsive Text', PTS_LANG_CODE); ?></span>
                                    <span class="sup-reduce-txt"><?php _e('Enb. Responsive Text', PTS_LANG_CODE); ?></span>
                                </label>
                            </div>
                            -->
                            <div class="ptsTableSetting supMd3 pts-ed-row">
                                <span class="pts-ed-label">
                                    <label for="ptsEdDisableTooltipStyle">
                                        <span class="sup-complete-txt"><?php _e('Disable Custom Tooltip Styles', PTS_LANG_CODE); ?></span>
                                        <span class="sup-reduce-txt"><?php _e('Disable Custom Tooltip Styles', PTS_LANG_CODE); ?></span>
                                    </label>
                                    <i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Disable supsystic styles for tooltips in your pricing table', PTS_LANG_CODE)); ?>"></i>
                                </span>
                                <span class="pts-ed-ctl">
                                    <?php echo htmlPts::checkbox('params[disable_custom_tooltip_style]', [
                                      'checked' => isset($this->table['params']['disable_custom_tooltip_style']) ? (int) $this->table['params']['disable_custom_tooltip_style']['val'] : 0,
                                      'attrs' => 'id="ptsEdDisableTooltipStyle"',
                                    ]); ?>
                                </span>
                            </div>
                        </section>
                    </div>
                    <div id="#toggle" class="toggle">
						<?php if (!$isPro) { ?>
							<?php if ($upsell) { ?>
								<?php echo $upsell->getProNote('toggle', __('A Monthly / Yearly switch that changes every price of the table is in Pricing Table PRO and in Data Tables Pro.', PTS_LANG_CODE), 'pts-ed-pro-note'); ?>
							<?php } else { ?>
								<div>
									<span class="ptsImportantText"><?php echo esc_html(__('To enable Toggle option order PRO version. ', PTS_LANG_CODE)); ?></span>
								</div>
								<div style="margin-top: 7px;">
									<a target="_blank" href="https://supsystic.com/plugins/pricing-table/" class="button">Get PRO</a>
								</div>
							<?php } ?>
						<?php } ?>
						<?php if (!$isPro): ?>
						<div class="ptsNotActiveWrapper">
						<?php endif; ?>
                        <section class="pts-ed-card">
                            <header class="pts-ed-card-head">
                                <span class="pts-ed-card-icon"><i class="fa fa-toggle-on" aria-hidden="true"></i></span>
                                <span class="pts-ed-card-title"><b><?php _e('Switch', PTS_LANG_CODE); ?></b><small><?php _e('A switch above or below the table that changes the prices.', PTS_LANG_CODE); ?></small></span>
                            </header>
							<div id="enableSwitchToggle" class="ptsTableSetting supMd3 pts-ed-row">
								<span class="pts-ed-label">
									<label for="ptsEdEnableSwitchToggle">
										<span class="sup-complete-txt"><?php _e('Enable Switch Toggle', PTS_LANG_CODE); ?></span>
										<span class="sup-reduce-txt"><?php _e('Enable Switch Toggle', PTS_LANG_CODE); ?></span>
									</label>
									<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Enable Switch Toggle.  You need add at list two options to use switch toggle button functionality!', PTS_LANG_CODE)); ?>"></i>
								</span>
								<span class="pts-ed-ctl">
									<?php echo htmlPts::checkbox('params[enable_switch_toggle]', [
           'checked' => isset($this->table['params']['enable_switch_toggle']) ? (int) $this->table['params']['enable_switch_toggle']['val'] : 0,
           'attrs' => 'class="ptsProOpt" id="ptsEdEnableSwitchToggle"',
         ]); ?>
								</span>
							</div>
							<div class="ptsTableSetting supMd3 pts-ed-row">
								<span class="pts-ed-label">
									<span class="sup-complete-txt"><?php _e('Text', PTS_LANG_CODE); ?></span>
									<span class="sup-reduce-txt"><?php _e('Text', PTS_LANG_CODE); ?></span>
									<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Text placed above the switch', PTS_LANG_CODE)); ?>"></i>
								</span>
								<span class="pts-ed-ctl">
									<?php echo htmlPts::text('params[switch_text]', [
           'value' => isset($this->table['params']['switch_text']) ? $this->table['params']['switch_text']['val'] : '',
           'attrs' => 'style="width: 100px"',
         ]); ?>
								</span>
							</div>
							<div class="ptsTableSetting supMd3 pts-ed-row">
								<?php if (!empty($this->table['params']['switch_type']['val']) && $this->table['params']['switch_type']['val'] == 'togglebutton') { ?>
									<script type="text/javascript">
										jQuery(document).ready(function(){
											setTimeout(function(){
												jQuery('[name="options"]').eq(0).trigger('change');
											},1000);
										});
									</script>
								<?php } ?>
								<span class="pts-ed-label">
									<?php _e('Select type', PTS_LANG_CODE); ?>
									<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Select switch type. Toggle button allows to show only first two options', PTS_LANG_CODE)); ?>"></i>
								</span>
								<span class="pts-ed-ctl">
									<?php echo htmlPts::selectbox('params[switch_type]', [
           'options' => ['dropdown' => 'Drop Down list', 'radiobutton' => 'Radio Button list', 'togglebutton' => 'Toggle button'],
           'value' => isset($this->table['params']['switch_type']) ? $this->table['params']['switch_type']['val'] : 'dropdown',
           'attrs' => 'class="chosen"',
         ]); ?>
								</span>
							</div>
							<div class="ptsTableSetting supMd3 pts-ed-row">
								<span class="pts-ed-label">
									<?php _e('Select position', PTS_LANG_CODE); ?>
									<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Choose vertical position for switch', PTS_LANG_CODE)); ?>"></i>
								</span>
								<span class="pts-ed-ctl">
									<?php echo htmlPts::selectbox('params[switch_position]', [
           'options' => ['top' => 'Top', 'bottom' => 'Bottom'],
           'value' => isset($this->table['params']['switch_position']) ? $this->table['params']['switch_position']['val'] : 'top',
           'attrs' => 'class="chosen"',
         ]); ?>
								</span>
							</div>
							<div class="ptsTableSetting supMd3 pts-ed-row">
								<span class="pts-ed-label">
									<?php _e('Align', PTS_LANG_CODE); ?>
									<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Select switch horizontal position', PTS_LANG_CODE)); ?>"></i>
								</span>
								<span class="pts-ed-ctl">
									<?php echo htmlPts::selectbox('params[switch_align]', [
           'options' => ['left' => 'Left', 'center' => 'Center', 'right' => 'Right'],
           'value' => isset($this->table['params']['switch_align']) ? $this->table['params']['switch_align']['val'] : 'center',
           'attrs' => 'class="chosen"',
         ]); ?>
								</span>
							</div>
                        </section>
                        <section class="pts-ed-card">
                            <header class="pts-ed-card-head">
                                <span class="pts-ed-card-icon"><i class="fa fa-tint" aria-hidden="true"></i></span>
                                <span class="pts-ed-card-title"><b><?php _e('Switch colors', PTS_LANG_CODE); ?></b><small><?php _e('Colors of the switch buttons and their text.', PTS_LANG_CODE); ?></small></span>
                            </header>
							<div class="ptsTableSetting supMd3 pts-ed-row">
								<span class="pts-ed-label">
									<span class="sup-complete-txt"><?php _e('Button border color', PTS_LANG_CODE); ?></span>
									<span class="sup-reduce-txt"><?php _e('Switch but. border color', PTS_LANG_CODE); ?></span>
									<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Choose switch button border color', PTS_LANG_CODE)); ?>"></i>
								</span>
								<span class="pts-ed-ctl">
									<?php echo htmlPts::hidden('params[switch_color_border]', [
           'attrs' => 'class="ptsSwitchColorBorder"',
           'value' => isset($this->table['params']['switch_color_border']) ? $this->table['params']['switch_color_border']['val'] : '',
         ]); ?>
									<div class="ptsTear ptsSwitchColorBorderTear"></div>
								</span>
							</div>
							<div class="ptsTableSetting supMd3 pts-ed-row">
								<span class="pts-ed-label">
									<span class="sup-complete-txt"><?php _e('Button color', PTS_LANG_CODE); ?></span>
									<span class="sup-reduce-txt"><?php _e('Switch but. color', PTS_LANG_CODE); ?></span>
									<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Choose switch button background color', PTS_LANG_CODE)); ?>"></i>
								</span>
								<span class="pts-ed-ctl">
									<?php echo htmlPts::hidden('params[switch_color_button]', [
          'attrs' => 'class="ptsSwitchColorButton"',
          'value' => isset($this->table['params']['switch_color_button']) ? $this->table['params']['switch_color_button']['val'] : '#b7b7b7',
        ]); ?>
									<div class="ptsTear ptsSwitchColorButtonTear"></div>
								</span>
							</div>
							<div class="ptsTableSetting supMd3 pts-ed-row">
								<span class="pts-ed-label">
									<span class="sup-complete-txt"><?php _e('Button text color', PTS_LANG_CODE); ?></span>
									<span class="sup-reduce-txt"><?php _e('Switch but. text color', PTS_LANG_CODE); ?></span>
									<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Choose switch button text color', PTS_LANG_CODE)); ?>"></i>
								</span>
								<span class="pts-ed-ctl">
									<?php echo htmlPts::hidden('params[switch_color_button_text]', [
          'attrs' => 'class="ptsSwitchColorButtonText"',
          'value' => isset($this->table['params']['switch_color_button_text']) ? $this->table['params']['switch_color_button_text']['val'] : '',
        ]); ?>
									<div class="ptsTear ptsSwitchColorButtonTextTear"></div>
								</span>
							</div>
							<div class="ptsTableSetting supMd3 pts-ed-row">
								<span class="pts-ed-label">
									<span class="sup-complete-txt"><?php _e('Button text color (not active)', PTS_LANG_CODE); ?></span>
									<span class="sup-reduce-txt"><?php _e('But. text color (not active)', PTS_LANG_CODE); ?></span>
									<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Choose switch button text color (not active position)', PTS_LANG_CODE)); ?>"></i>
								</span>
								<span class="pts-ed-ctl">
									<?php echo htmlPts::hidden('params[switch_color_button_text_noactive]', [
          'attrs' => 'class="ptsSwitchColorButtonTextNoactive"',
          'value' => isset($this->table['params']['switch_color_button_text_noactive']) ? $this->table['params']['switch_color_button_text_noactive']['val'] : '',
        ]); ?>
									<div class="ptsTear ptsSwitchColorButtonTextNoactiveTear"></div>
								</span>
							</div>
                        </section>
                        <section class="pts-ed-card pts-ed-card-wide">
                            <header class="pts-ed-card-head">
                                <span class="pts-ed-card-icon"><i class="fa fa-list-ul" aria-hidden="true"></i></span>
                                <span class="pts-ed-card-title"><b><?php _e('Switch options', PTS_LANG_CODE); ?></b><small><?php _e('The options of the switch, for example Monthly and Yearly; drag to sort them.', PTS_LANG_CODE); ?></small></span>
                            </header>
							<?php echo htmlPts::hidden('params[option_name_input]', [
         'value' => isset($this->table['params']['option_name_input']) ? htmlspecialchars($this->table['params']['option_name_input']['val']) : '',
       ]); ?>
							<div class="field-options-container sc-hidden">
								<div class="supRow mbsModalEditAddToDdOpt">
									<div class="supMd2">
										<label class="ptsLabel"><?php _e('Switch name', PTS_LANG_CODE); ?></label>
									</div>
									<div class="supMd4">
										<div class="ptsInput-row">
											<input class="ptsInput option-name-input" type="text" name="name" value="">
											<p class="sc-hidden error-msg"></p>
										</div>
									</div>
									<div class="supMd4">
										<div class="ptsInput-row">
											<button class="sc-button primary add-field-option">
												<i class="fa fa-plus-circle"></i>
											</button>
										</div>
									</div>
								</div>
								<?php
        $optionNameInput = isset($this->table['params']['option_name_input']) ? $this->table['params']['option_name_input']['val'] : '';
        if ($optionNameInput) {
          $optionsArray = utilsPts::jsonDecode($optionNameInput);
          if (empty($optionsArray) || !$optionsArray) {
            $optionsArray = utilsPts::jsonDecode(base64_decode($optionNameInput));
          }
        } else {
          $optionsArray = false;
        }
        ?>
								<div class="supRow ptsInput-row">
									<div class="supMd11">
										<div class="field-options-list">
											<?php if ($optionsArray) { ?>
												<?php
            $selectedName = '';
            if (!empty($optionsArray['selected_options'])) {
              if (is_array($optionsArray['selected_options'])) {
                $i = 0;
                foreach ($optionsArray['selected_options'] as $select) {
                  if ($i === 0) {
                    $selectedName = $select;
                  }
                  $i++;
                }
              } else {
                $selectedName = $optionsArray['selected_options'];
              }
            }
            ?>
												<?php if (!empty($optionsArray['options']) && !is_array($optionsArray['options'])) { ?>
													<?php if ($selectedName === $optionsArray['options']) {
               $checked = 'checked';
             } else {
               $checked = '';
             } ?>
													<div class="option" style="display: flex;">
														<div class="option-drag-handler">
															<i class="fa fa-arrows-v"></i>
														</div>
														<div class="option-name">
															<input type="text" name="options" value="<?php echo esc_attr($optionsArray['options']); ?>">
														</div>
														<div class="checked-state">
															<label class="sc-checkbox seleceted-options-state">
																<input type="radio" name="selected_options" value="<?php echo esc_attr($optionsArray['options']); ?>" <?php echo $checked; ?>>
																<?php _e('Default selected', PTS_LANG_CODE); ?>
															</label>
														</div>
														<div class="remove-option">
															<i class="fa fa-trash-o"></i>
														</div>
													</div>
												<?php } else {foreach ($optionsArray['options'] as $option) {
              $optionName = is_array($option) ? ($option['name'] ?? '') : $option;
              $optionKey = is_array($option) ? ($option['key'] ?? '') : $option;
              ?>
														<?php if ($selectedName === $optionKey) {
                $checked = 'checked';
              } else {
                $checked = '';
              } ?>
														<div class="option" style="display: flex;">
															<div class="option-drag-handler">
																<i class="fa fa-arrows-v"></i>
															</div>
															<div class="option-name">
																<input type="text" name="options" value="<?php echo esc_attr($optionName); ?>">
															</div>
															<div class="checked-state">
																<label class="sc-checkbox seleceted-options-state">
																	<input type="radio" name="selected_options" value="<?php echo esc_attr($optionKey); ?>" <?php echo $checked; ?>>
																	<?php _e('Default selected', PTS_LANG_CODE); ?>
																</label>
															</div>
															<div class="remove-option">
																<i class="fa fa-trash-o"></i>
															</div>
															<input type="hidden" name="option_key" value="<?php echo esc_attr($optionKey); ?>">
														</div>
													<?php } ?>
												<?php } ?>
											<?php } ?>
										</div>
									</div>
								</div>

								<div class="option-template ptsHidden">
									<div class="option" style="display: flex;">
										<div class="option-drag-handler">
											<i class="fa fa-arrows-v"></i>
										</div>
										<div class="option-name">
											<input type="text" name="options">
										</div>
										<div class="checked-state">
											<label class="sc-checkbox seleceted-options-state">
												<input type="radio" name="selected_options" value="true">
												<?php _e('Default selected', PTS_LANG_CODE); ?>
											</label>
										</div>
										<div class="remove-option">
											<i class="fa fa-trash-o"></i>
										</div>
									</div>
								</div>
							</div>
                        </section>

						<?php if (!$isPro)://end .ptsNotActiveWrapper
         ?>
						</div>
						<?php endif; ?>
                    </div>

                </div>
				<div style="clear: both;"></div>
			</div>
			<div id="ptsCanvas" class="clearfix">
				<?php echo $this->renderedTable; ?>
			</div>
		</div>
	</div>
</section>
<div id="ptsTableEditorFooter">
	<?php echo $this->editorFooter; ?>
</div>
<div id="ptsTableAllColsHaveBgColorWnd" style="display: none;" title="<?php _e('Notice', PTS_LANG_CODE); ?>">
	<p><?php _e(
   'Please be adviced that all columns in your table have enabled Fill color feature - so changing background color for table may not influence to all table view, or maybe will not influence to table view at all (depending of template that you selected for your table).',
   PTS_LANG_CODE,
 ); ?></p>
</div>
