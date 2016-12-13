/* 
 * Copyright (C) 2016 IuriiP <hardwork.mouse@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

var localTree = {};

(function ($, localTree) {

    'use strict';

    var nodes = [],
            levels = [],
            ctx,
            height = 48;

    function sortLevels(nums) {
        for (var ii = 0; ii < nums.length; ii++) {
            var num = nums[ii],
                    node = nodes.find(function (e) {
                        return e.no === num;
                    }),
                    level = node.level;

            if (!levels[level]) {
                levels[level] = [num];
            } else {
                levels[level].push(num);
            }
            node.serial = levels[level].length - 1;

            if (node.children.length > 0) {
                sortLevels(node.children);
            }
        }
    }
    ;

    localTree.generate = function (cnt) {
        for (var ii = 1; ii < cnt; ii++) {
            var theParent = Math.floor(Math.random() * Math.max(0,ii-5));
            nodes[ii] = {
                no: ii,
                id: 'node_' + ii,
                parent: theParent,
                children: [],
                level: nodes[theParent].level + 1
            };
            nodes[theParent].children.push(ii);
        }
    };

    localTree.clear = function () {
        $('.nodeRow').remove();

        nodes = [];
        nodes[0] = {
            no: 0,
            id: 'node_0',
            parent: null,
            children: [],
            level: 0
        };

    };

    localTree.fillTable = function (baseRow) {
        for (var ii = 0; ii < nodes.length; ii++) {
            var node = nodes[ii];
            baseRow.parent().append('<tr class="nodeRow"><td>' + node.no + '</td><td>' + node.parent + '</td><td>' + node.children + '</td></tr>');
        }
    };

    localTree.drawNode = function (num, color, textcolor) {
        var node = nodes.find(function (e) {
            return e.no == num;
        }),
                x = node.center.x,
                y = node.center.y,
                text = node.no;

        ctx.fillStyle = color;
        ctx.fillRect(x - height / 4, y, height / 2, height / 2);
        ctx.fillStyle = textcolor;
        ctx.font = height / 4 + "px Arial";
        ctx.fillText(text, x, y + height / 4);
    };

    localTree.drawVisual = function (canvas) {
        ctx = canvas[0].getContext("2d");
        ctx.textAlign = "center";
        var width = ctx.canvas.width;

        ctx.clearRect(0, 0, width, ctx.canvas.height);

        levels = [];
        sortLevels([0]);

        console.log(nodes);
        console.log(levels);

        for (var level = 0; level < levels.length; level++) {
            var stage = levels[level],
                    step = Math.floor(width / stage.length);
            for (var ii = 0; ii < stage.length; ii++) {
                var node = nodes.find(function(e) { return e.no == stage[ii];}),
                        parent = nodes.find(function(e) { return e.no == node.parent;}),
                        xxx = (ii + 0.5) * step,
                        yyy = height * level;

                node.center = {
                    x: xxx,
                    y: yyy
                };

                localTree.drawNode(node.no, "#00FF00", "#FF0000");

                if (parent) {
                    ctx.beginPath();
                    ctx.moveTo(xxx, yyy);
                    ctx.lineTo(parent.center.x, parent.center.y + 24);
                    ctx.strokeStyle = "#0000FF";
                    ctx.stroke();
                }
            }
        }
    };

    function removeLeaf(tree, idx) {
        console.log('remove: ' + idx);

        var lix = tree.findIndex(function(e) { return e.no == idx; });
        if (lix !== 'undefined') {
            tree.splice(lix, 1);
        }

        var parent = tree.find(function(e) { return e.children.indexOf(idx) != -1;});
        var lix = parent.children.indexOf( idx );
        parent.children.splice(lix, 1);

    }

    localTree.findCenter1 = function () {
        var dstart = new Date();
//        alert('pause');
        var tree = nodes.slice();
        for (var ii = 0; ii < tree.length; ii++) {
            tree[ii].children = tree[ii].children.slice();
        }

        while (tree.length > 2) {
            var leaves = [];
            for (var ii = 0; ii < tree.length; ii++) {
                if (tree[ii].children.length < 1) {
                    leaves.push(tree[ii].no);
                }
            }
            for (var ii = 0; ii < leaves.length; ii++) {
                removeLeaf(tree, leaves[ii]);
            }
        }
        var dfinish = new Date();
        $("#result").append((dfinish.getTime() - dstart.getTime()) + '<br />');

        if (tree.length > 1) {
            return [tree[0].no, tree[1].no];
        } else {
            return [tree[0].no];
        }
        return [4];
    };

})(jQuery, localTree);

$(
        // on ready
                function () {
                    $('#generateTree').click(function () {
                        localTree.clear();
                        localTree.generate($('#nodeCounter').val());
                        localTree.fillTable($('#headRow'));
                        localTree.drawVisual($('#canvas'));
                    });
                    $('#findCenter1').click(function () {
                        localTree.drawVisual($('#canvas'));
                        var centers = localTree.findCenter1();
                        for (var ii = 0; ii < centers.length; ii++) {
                            localTree.drawNode(centers[ii], "#800000", "#FFFFFF");
                        }
                    });
                }
        );