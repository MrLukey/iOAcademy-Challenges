<?php

const POST_SIZE = 0.1;
const RAILING_SIZE = 1.5;
const DISTANCE_TO_SUN = 149600000000;
const CIRCUMFERENCE_OF_EARTH = 40075000;

function exit_FM() {
    echo "\nGoodbye...\n";
    exit(0);
}

function display_Help() {
    echo "\nNo help is coming for you...";
}

function calculate_Length(int $posts, int $railings): array {
    $max_railings = min($posts - 1, $railings);
    $max_posts = min($railings + 1, $posts);
    $s =  ($posts - $max_posts > 1 || $railings - $max_railings > 1) ? 's' : '';
    if ($posts > $max_posts) echo "\n" . ($posts - $max_posts) . " post$s will not be used, build more railings.";
    elseif ($railings > $max_railings) echo "\n" . ($railings - $max_railings) . " railing$s will not be used, "
        . 'build more posts.';
    return [$max_posts, $max_railings, ($max_railings * RAILING_SIZE) + ($max_posts * POST_SIZE)];
}

function calculate_Fencing(float $length): array {
    $remainder = $length - 0.1;
    $railings = (int) ($remainder / (POST_SIZE + RAILING_SIZE));
    $remainder -= $railings * (POST_SIZE + RAILING_SIZE);
    $posts = $railings + 1;
    if ($remainder){
        $posts++;
        $railings++;
        $remainder -= POST_SIZE + RAILING_SIZE;
    }
    $length_added = abs(round($remainder, 2));
    return [number_format($posts), number_format($railings), $length_added];
}

function get_Length($failed_length): float {
    if ($failed_length || $failed_length == '0') echo "\n'$failed_length' is not an number > 0.1";
    echo "\nPlease specify required length of fence (m):\n>>>  ";
    $length = rtrim(fgets(STDIN));
    if (in_array($length, ['q','Q'])) exit_FM();
    if ($length < 0.1) $length = get_Length($length);
    elseif ($length == 0.1) {
        echo "\nYou want a fence made of a single post? Come on now... Try again.";
        $length = get_Length(null);
    } elseif ($length > 2 * DISTANCE_TO_SUN) {
        echo "\nA fence long enough to stretch to the Sun and back is obscene. Sort your life out...";
        $length = get_Length(null);
    } elseif ($length > 10 * CIRCUMFERENCE_OF_EARTH){
        echo "\nNot sure if a fence that circles the earth ten times is what humanity really needs right now... ";
        $length = get_Length(null);
    }
    return round($length,2);
}

function get_Railings($failed_railings): int {
    if ($failed_railings || $failed_railings == '0')
        echo "\n'$failed_railings' is not valid. At least 1 railing is needed to make a fence.";
    echo "\nPlease specify number of railings:\n>>>  ";
    $railings = rtrim(fgets(STDIN));
    if (in_array($railings, ['q','Q'])) exit_FM();
    if ($railings < 1) $railings = get_Railings($railings);
    $remainder = $railings - (int) $railings;
    if ($remainder !== 0) echo "\n$remainder of a railing doesn't make much sense, we'll just ignore it.";
    return (int) $railings;
}

function get_Posts($failed_posts): int {
    if ($failed_posts || $failed_posts == '0')
        echo "\n'$failed_posts' is not valid. At least 2 posts are needed to make a fence.";
    echo "\nPlease specify number of posts:\n>>>  ";
    $posts = rtrim(fgets(STDIN));
    if (in_array($posts, ['q','Q'])) exit_FM();
    if ($posts < 2) $posts = get_Posts($posts);
    $remainder = $posts - (int) $posts;
    if ($remainder !== 0) echo "\n$remainder of a post doesn't make much sense, we'll ignore it.";
    return (int) $posts;
}

function length_Mode() {
    echo "\nLength mode selected:\nPress [q/Q] to quit.\n";
    $length = get_Length(null);
    [$posts, $railings, $length_added] = calculate_Fencing($length);
    if ($length_added) echo "\nAn additional $length_added" . 'm of fencing is required to span ' .
        number_format($length) . 'm';
    $s = $railings > 1 ? 's' : '';
    echo "\nA fence of " . number_format($length + $length_added) . "m will use $posts posts and $railings railing$s.\n";
}

function post_Mode() {
    echo "\nPost mode selected:\nPress [q/Q] to quit.\n";
    $posts = get_Posts(null);
    $railings = get_Railings(null);
    $fence_data = calculate_Length($posts, $railings);
    $s = $fence_data[1] > 1 ? 's' : '';
    echo "\n$fence_data[0] posts and $fence_data[1] railing$s makes for $fence_data[2]" . "m of fencing.\n";
}

function set_Mode($failed_string): string
{
    if ($failed_string) echo "\n'$failed_string' is not a valid option.";
    echo "\nPost-mode: [p|P]    Length mode: [l|L]    Help: [h|H]    Quit: [q|Q]\n>>>  ";
    $mode = rtrim(fgets(STDIN));
    if (!in_array($mode, ['p', 'P', 'l', 'L', 'h', 'H', 'q', 'Q'])) $mode = set_Mode($mode);
    if (in_array($mode, ['q', 'Q'])) exit_FM();
    if (in_array($mode, ['h', 'H'])) {
        Display_Help();
        $mode = Set_Mode(null);
    }
    if(in_array($mode, ['p','P'])) return 'P';
    if(in_array($mode, ['l', 'L'])) return 'L';
    return 'UNKNOWN_FAILURE';
}

function fence_master() {
    echo "\nWelcome to Fence Master\n";
    $mode_select = set_Mode(null);
    if ($mode_select === 'P') post_Mode(); elseif ($mode_select === 'L') length_Mode();
    else echo "\nAn unknown error occurred.";
    exit_FM();
}

fence_master();