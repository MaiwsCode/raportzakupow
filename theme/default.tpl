<head>
    <link rel="stylesheet" type='text/css' href='{$css}/planer/default.css' />
    
</head>

<div class="layer" style="padding: 9px; width: 98%;">
<div style="text-align: left; height:30px">
<div class="css3_content_shadow">
<div style="padding: 5px; background-color: #FFFFFF;">

    {$select}
    {$monthSelect}
    {$yearSelect}
<br>
<br><br>
<h3>&nbsp;</h3>
<h3> Handlowiec: {$who} </h3>
<h3> Miesiąc: {$month} </h3>
<h3> Rok: {$year} </h3>


<br>
 <table class="ttable ttable-hover ttable-bordered" style="margin-top:15px;margin-bottom:15px;user-select: text;"> 
 <thead>
        <th> {$dateOrder}  {if $orders.planed_purchase_date == 1 } &darr; {elseif $orders.planed_purchase_date == 2} &uarr; {else}  &#8645; {/if} </td>
        <th> Klient </td>
        <th> Ilość </td>
        <th> {$statusOrder}  {if $orders.status == 1 } &darr; {elseif $orders.status == 2} &uarr; {else}  &#8645; {/if} </td>
    </thead>
     <tbody>
    {foreach from=$purchases item=purchase}
        <tr>
            <td>   {$purchase.planed_purchase_date} </td>
            <td >    {$purchase.company} </td>
            <td  >   {$purchase.amount} </td>
            {if $purchase.status == 'purchased'}
                <td>  Kupione  </td>
            {else}
                <td>   Kupione niepotwierdzone </td>
            {/if}
        </tr>
    {/foreach}
    </tbody>


 </table>