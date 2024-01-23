"use client";
import React, { useState } from "react";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import axios from "axios";
import { toast } from "react-toastify";
import { REGISTER_URL } from "@/lib/ApiEndPoints";

export default function Register() {
  const [loading, setLoading] = useState<boolean>(false);
  const [authState, setAuthState] = useState<AuthStateType>({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
  });
  const [errors, setErrors] = useState({
    name: [],
    email: [],
    password: [],
  });

  const handleSubmit = async (event: React.FormEvent) => {
    event.preventDefault();
    setLoading(true);
    await axios
      .post(REGISTER_URL, authState, {
        headers: {
          Accept: "application/json",
        },
      })
      .then((res) => {
        toast.success("Conta criada com sucesso.", {
          theme: "colored",
        });
        setLoading(false);
        setAuthState({});
      })
      .catch((err) => {
        setLoading(false);
        console.log("Ocorreu um erro ", err.response?.data);
        setErrors(err.response?.data?.errors);
      });
  };
  return (
    <Card>
      <CardHeader>
        <CardTitle>Cadastrar</CardTitle>
        <CardDescription>
          Bem vindo ao Medium
        </CardDescription>
      </CardHeader>
      <CardContent className="space-y-2">
        <form onSubmit={handleSubmit}>
          <div className="space-y-1">
            <Label htmlFor="name">Nome</Label>
            <Input
              id="name"
              type="text"
              placeholder="Digite seu nome.."
              onChange={(e) =>
                setAuthState({ ...authState, name: e.target.value })
              }
            />
            <span className="text-red-500">{errors?.name?.[0]}</span>
          </div>
          <div className="space-y-1">
            <Label htmlFor="email">Email</Label>
            <Input
              id="email"
              type="email"
              placeholder="Digite seu email.."
              onChange={(e) =>
                setAuthState({ ...authState, email: e.target.value })
              }
            />
            <span className="text-red-500">{errors?.email?.[0]}</span>
          </div>
          <div className="space-y-1">
            <Label htmlFor="password">Senha</Label>
            <Input
              id="password"
              type="password"
              placeholder="Informe uma senha.."
              onChange={(e) =>
                setAuthState({ ...authState, password: e.target.value })
              }
            />
            <span className="text-red-500">{errors?.password?.[0]}</span>
          </div>
          <div className="space-y-1">
            <Label htmlFor="cPasword">Confirme a senha</Label>
            <Input
              id="cPasword"
              type="password"
              placeholder="Confirme a senha.."
              onChange={(e) =>
                setAuthState({
                  ...authState,
                  password_confirmation: e.target.value,
                })
              }
            />
          </div>
          <div className="mt-4">
            <Button className="w-full" disabled={loading}>
              {loading ? "Processando.." : "Cadastrar"}
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>
  );
}