import { useState } from "react";
import Link from "next/link";

const Header = () => {
  const [isOpen, setIsOpen] = useState(false);

  const courses = [
    { name: "Web Development", link: "/courses/web-development" },
    { name: "AI & Machine Learning", link: "/courses/ai-ml" },
    { name: "Cyber Security", link: "/courses/cyber-security" },
    { name: "Electronics Engineering", link: "/courses/electronics" },
  ];

  return (
    <header className="bg-blue-600 text-white p-4 flex justify-between items-center">
      <h1 className="text-xl font-bold">Online Learning</h1>
      <div className="relative">
        <button
          className="bg-blue-500 px-4 py-2 rounded hover:bg-blue-700"
          onClick={() => setIsOpen(!isOpen)}
        >
          Browse
        </button>
        {isOpen && (
          <ul className="absolute bg-white text-black mt-2 py-2 w-48 shadow-lg rounded">
            {courses.map((course, index) => (
              <li key={index} className="px-4 py-2 hover:bg-gray-200">
                <Link href={course.link}>{course.name}</Link>
              </li>
            ))}
          </ul>
        )}
      </div>
    </header>
  );
};

export default Header;
