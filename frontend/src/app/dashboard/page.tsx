import React from "react";

import {
  CustomSession,
  authOptions,
} from "../api/auth/[...nextauth]/authOptions";
import { getServerSession } from "next-auth";
import AppNav from "@/components/common/AppNav";
import { fetchUserPosts } from "@/lib/ApiService";
import { formatDate, getImageUrl } from "@/lib/utils";
import Image from "next/image";
import PostTable from "@/components/dashboard/PostTable";

export default async function Dashboard() {
    const session: CustomSession | null = await getServerSession(authOptions);
    const userPosts: Array<Post> | [] | null = await fetchUserPosts(
      session?.user!.id!,
      session!.user!
    );
  return (
    <div>
        <AppNav session={session} />

        <div className="container mt-2">
        {userPosts && userPosts.length > 0 && (
          <PostTable posts={userPosts} user={session?.user!} />
        )}
      </div>
    </div>
  );
}
