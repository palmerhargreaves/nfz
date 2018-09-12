<?php if (count($concepts) > 0): ?>
    <h2>Концепции</h2>
    <div id="concepts" class="active">
        <table id="concepts-list" class="models">
            <thead>
            <tr>
                <td width="75">
                    <div class="has-sort">Дата</div>
                    <div class="sort has-sort"></div>
                </td>
                <td width="146">
                    <div class="has-sort">Дилер</div>
                    <div class="sort has-sort"></div>
                </td>
                <td>
                    <div>Действие</div>
                </td>
                <td width="35">
                    <div></div>
                </td>
                <td width="35">
                    <div></div>
                </td>
                <td width="35">
                    <div>
                        <div class="has-sort">&nbsp;</div>
                    </div>
                </td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($concepts as $n => $concept): ?>
                <?php $discussion = $concept->getDiscussion() ?>
                <?php $new_messages_count = $discussion ? $discussion->countUnreadMessages($sf_user->getAuthUser()->getRawValue()) : 0 ?>
                <tr class="sorted-row model-row concept-row<?php if ($n % 2 == 0) echo ' even' ?>"
                    data-model="<?php echo $concept->getId() ?>"
                    data-discussion="<?php echo $concept->getDiscussionId() ?>"
                    data-new-messages="<?php echo $new_messages_count ?>">
                    <td data-sort-value="<?php echo D::toUnix($concept->created_at) ?>">
                        <div class="date"><?php echo D::toLongRus($concept->created_at) ?></div>
                    </td>
                    <td data-sort-value="<?php echo $concept->getDealer()->getName() ?>"><?php echo $concept->getDealer()->getName(), ' (', $concept->getDealer()->getNumber(), ')' ?></td>
                    <td class="darker">
                        <div><?php echo $concept->getSpecialistActionText() ?></div>
                        <div class="sort"></div>
                    </td>
                    <td class="darker">
                        <div class="<?php echo $concept->getCssStatus() ?>"></div>
                    </td>
                    <td class="darker">
                        <div class="<?php echo $concept->getReportCssStatus() ?>"></div>
                    </td>
                    <td data-sort-value="<?php echo $new_messages_count ?>" class="darker">
                        <?php if ($new_messages_count > 0): ?>
                            <div class="message"><?php echo $new_messages_count ?></div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script type="text/javascript">
        $(function () {
            new TableSorter({
                selector: '#concepts-list'
            }).start();
        });
    </script>
<?php endif; ?>
        
