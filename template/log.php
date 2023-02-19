<div class="log0wrap">
    <table>
        <tbody>
        <tr>
            <td>Date</td>
            <td>Content</td>
        </tr>
        <?php
        if (log::getLog()) {
            foreach (json_decode(log::getLog(), true) as $keyl => $valuel) {
                $code = json_decode($valuel['data']);

                echo '<tr>';
                echo '<td class="log-date">' . date("Y-m-d H:i:s", $valuel["date"]) . '</td>';
                echo '<td  class="log-data">' . esc_attr($valuel['data']) . '</td>';
                echo '</tr>';
            }
        }
        ?>
        </tbody>
    </table>
</div>