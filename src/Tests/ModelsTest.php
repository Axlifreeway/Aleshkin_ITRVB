<?php

use PHPUnit\Framework\TestCase;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use Ramsey\Uuid\Uuid;

class ModelsTest extends TestCase {
    public function testUserModel(): void {
        $user = new User(Uuid::uuid4()->toString(), 'John', 'Doe');
        $this->assertEquals('John', $user->firstName);
        $this->assertEquals('Doe', $user->lastName);
    }

    public function testPostModel(): void {
        $post = new Post(Uuid::uuid4()->toString(), 'author-uuid', 'Title', 'Content');
        $this->assertEquals('Title', $post->title);
        $this->assertEquals('Content', $post->text);
    }

    public function testCommentModel(): void {
        $comment = new Comment(Uuid::uuid4()->toString(), 'post-uuid', 'author-uuid', 'Comment Text');
        $this->assertEquals('Comment Text', $comment->text);
    }
}