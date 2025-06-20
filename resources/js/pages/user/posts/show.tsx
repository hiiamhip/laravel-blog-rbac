import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { toast, Toaster } from 'sonner';

interface Comment {
    id: number;
    content: string;
    user: {
        id: number;
        name: string;
    };
    created_at: string;
}

interface Post {
    id: number;
    title: string;
    content: string;
    user: {
        id: number;
        name: string;
    };
    category: {
        id: number;
        name: string;
    };
    comments: Comment[];
    comments_count: number;
    created_at: string;
}

interface PostShowProps {
    post: Post;
}

interface PageProps {
    auth: {
        user: {
            id: number;
            name: string;
            role: string;
        };
    };
    [key: string]: any;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: '/posts',
    },
    {
        title: 'View Post',
        href: '#',
    },
];

export default function PostShow({ post }: PostShowProps) {
    const [comment, setComment] = useState('');
    const { auth } = usePage<PageProps>().props;

    console.log(auth);

    const handleSubmitComment = (e: React.FormEvent) => {
        e.preventDefault();

        router.post(
            route('posts.comments.store', post.id),
            {
                content: comment,
            },
            {
                onSuccess: () => {
                    setComment('');
                    toast.success('Comment added successfully');
                },
                onError: (errors) => {
                    toast.error(errors.content || 'Failed to add comment');
                },
            },
        );
    };

    const handleDeleteComment = (commentId: number) => {
        if (confirm('Are you sure you want to delete this comment?')) {
            router.delete(route('comments.destroy', commentId), {
                onSuccess: () => {
                    toast.success('Comment deleted successfully');
                },
                onError: () => {
                    toast.error('Failed to delete comment');
                },
            });
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={post.title} />
            <Toaster richColors closeButton position="top-right" />
            <div className="flex flex-col gap-4 p-4">
                <div className="mb-4 flex items-center justify-between">
                    <h1 className="text-2xl font-bold">{post.title}</h1>
                    <Button variant="outline" onClick={() => router.visit(route('posts.index'))}>
                        Back to Posts
                    </Button>
                </div>

                <div className="rounded-lg bg-white p-6 shadow">
                    <div className="mb-4 flex items-center gap-4 text-sm text-gray-500">
                        <span className="text-xl font-extrabold text-black">{post.user.name}</span>
                        <span>•</span>
                        <span>{post.category.name}</span>
                        <span>•</span>
                        <span>{new Date(post.created_at).toLocaleDateString()}</span>
                        <span>•</span>
                        <span>{post.comments_count} comments</span>
                    </div>

                    <hr />
                    <div className="prose mt-2 mb-8 max-w-none">{post.content}</div>
                    <hr />

                    {/* Comments Section */}
                    <div className="mt-8">
                        <h2 className="mb-4 text-xl font-semibold">Comments</h2>

                        {/* Add Comment Form */}
                        <form onSubmit={handleSubmitComment} className="mb-8">
                            <Textarea
                                value={comment}
                                onChange={(e) => setComment(e.target.value)}
                                placeholder="Write a comment..."
                                className="mb-2"
                                required
                            />
                            <Button type="submit">Post Comment</Button>
                        </form>

                        {/* Comments List */}
                        <div className="space-y-4">
                            {post.comments.map((comment) => (
                                <div key={comment.id} className="rounded-lg bg-gray-50 p-4">
                                    <div className="mb-2 flex items-start justify-between">
                                        <div>
                                            <span className="font-semibold">{comment.user.name}</span>
                                            <span className="ml-2 text-sm text-gray-500">{new Date(comment.created_at).toLocaleDateString()}</span>
                                        </div>
                                        {(comment.user.id === auth.user.id || auth.user.role === 'admin') && (
                                            <Button variant="ghost" size="sm" onClick={() => handleDeleteComment(comment.id)}>
                                                Delete
                                            </Button>
                                        )}
                                    </div>
                                    <p className="text-gray-700">{comment.content}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
