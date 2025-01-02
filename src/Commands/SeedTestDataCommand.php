<?php

namespace App\Commands;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Repositories\UserRepository;
use App\Repositories\PostRepository;
use App\Repositories\CommentsRepository;
use App\Logging\FileLogger;
use Faker\Factory as FakerFactory;
use PDO;

class SeedTestDataCommand {
    private PDO $db;
    private UserRepository $userRepository;
    private PostRepository $postRepository;
    private CommentsRepository $commentsRepository;

    public function __construct(PDO $db) {
        $this->db = $db;
        $logger = new FileLogger(dirname(__DIR__) . '/logs/app.log');
        $this->userRepository = new UserRepository($db, $logger);
        $this->postRepository = new PostRepository($db, $logger);
        $this->commentsRepository = new CommentsRepository($db, $logger);

    }

    public function execute(array $options): void {
        $faker = FakerFactory::create();

        $usersNumber = $options['users-number'] ?? 10;
        $postsNumber = $options['posts-number'] ?? 20;

        echo "Generating $usersNumber users...\n";
        $users = [];
        for ($i = 0; $i < $usersNumber; $i++) {
            $user = new User($faker->uuid, $faker->name, $faker->email);
            $this->userRepository->save($user);
            $users[] = $user;
        }

        echo "Generating $postsNumber posts...\n";
        $posts = [];
        for ($i = 0; $i < $postsNumber; $i++) {
            $post = new Post($faker->uuid, $faker->sentence, $faker->paragraph, $faker->randomElement($users)->uuid);
            $this->postRepository->save($post);
            $posts[] = $post;
        }

        echo "Generating comments for posts...\n";
        foreach ($posts as $post) {
            $commentsNumber = $faker->numberBetween(0, 10); // Комментарии к каждому посту (0–10)
            for ($j = 0; $j < $commentsNumber; $j++) {
                $comment = new Comment(
                    $faker->uuid,
                    $post->uuid,
                    $faker->randomElement($users)->uuid,
                    $faker->sentence
                );
                $this->commentsRepository->save($comment);
            }
        }

        echo "Test data generation completed!\n";
    }
}
